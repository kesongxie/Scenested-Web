//
//  CropCoverPhotoViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/27/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CropCoverPhotoViewController: UIViewController {

    @IBOutlet weak var scrollView: UIScrollView!

    var imageViewToBeCropped = UIImageView()
    var image: UIImage?
    
    private var imageOffsetInMinScale = cropImageOffset()

    private var imageOffsetInWholeScale: cropImageOffset {
        get{
            var offsetInWholeScale = cropImageOffset()
            if let selectedImage = image {
                offsetInWholeScale.offSetY = imageOffsetInMinScale.offSetY * selectedImage.size.height / imageViewToBeCropped.frame.size.height
                offsetInWholeScale.offSetX = imageOffsetInMinScale.offSetX * selectedImage.size.width / imageViewToBeCropped.frame.size.width
            }
            return offsetInWholeScale
        }
    }
    
    

    
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        scrollView.delegate = self
        if let imageToBeCropped = image{
            let viewWidth = self.view.bounds.size.width
            let viewHeight = self.view.bounds.size.height
            
            imageViewToBeCropped.image = imageToBeCropped
            if imageToBeCropped.aspectRatio < StyleSchemeConstant.profileCoverPhotoInfo.aspectRatio{
                //the image is vertical, so need to scroll vertically to adjust the visible portion of the cover to be cropped
                let scaleWidth = viewWidth
                let scaleHeight = viewWidth / imageToBeCropped.aspectRatio
                let croppingAreaHeight = viewWidth / StyleSchemeConstant.profileCoverPhotoInfo.aspectRatio
                let contentOffSetY = ( scaleHeight - croppingAreaHeight) / 2
                imageViewToBeCropped.frame = CGRect(x: 0, y: 0, width: scaleWidth, height: scaleHeight)
                
                scrollView.contentInset = UIEdgeInsets(top: (viewHeight - croppingAreaHeight) / 2, left: 0, bottom: (viewHeight - croppingAreaHeight) / 2, right: 0)
                scrollView.contentOffset.y += contentOffSetY
                scrollView.contentSize = CGSize(width: scaleWidth, height: scaleHeight)
                imageOffsetInMinScale.offSetY = (view.bounds.size.height - view.bounds.size.width / StyleSchemeConstant.profileCoverPhotoInfo.aspectRatio) / 2 - (viewHeight - scaleHeight) / 2
            }else{
                print("horizon")
            }
            
            scrollView.addSubview(imageViewToBeCropped)

            
            if let cropCoverView = view as? CropCoverView{
                cropCoverView.cancelBtn.addTarget(self, action: #selector(CropCoverPhotoViewController.cancelBtnTapped), forControlEvents: .TouchUpInside)
                cropCoverView.doneBtn.addTarget(self, action: #selector(CropCoverPhotoViewController.doneBtnTapped), forControlEvents: .TouchUpInside)
                
            }

            
        }
        
        
        
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
    
    func cancelBtnTapped(){
        self.dismissViewControllerAnimated(true, completion: nil)
    }
    
    func doneBtnTapped(){
        if let profileNaviVC = ((self.presentingViewController as! UIImagePickerController).presentingViewController as! TabBarController).selectedViewController as? ProfileNavigationController{
            if let profileVC = profileNaviVC.viewControllers.first as? ProfileViewController{
                profileVC.dismissViewControllerAnimated(true, completion: nil)
                if let imageAfterCropped = cropCoverImage(image!){
                   
                    print(imageAfterCropped)
                    profileVC.profileCover.image = imageAfterCropped
                    //save imageAfterCropped to the server
                }
            }
            
        }
        
    }
    
    
    func cropCoverImage(image: UIImage) -> UIImage?{
        var clipRect: CGRect = CGRectZero
        let clipRectAspectRatio = StyleSchemeConstant.profileCoverPhotoInfo.aspectRatio
        
        
        if image.aspectRatio < clipRectAspectRatio{
            //portrait refer to the visible rect
            
            
            switch image.imageOrientation{
            case .Up:
                clipRect = CGRect(x: 0, y: imageOffsetInWholeScale.offSetY, width: image.size.width, height: image.size.width / clipRectAspectRatio)
           
            case .Right:
                 clipRect = CGRect(x: imageOffsetInWholeScale.offSetY , y: 0, width: image.size.width / clipRectAspectRatio , height: image.size.width)
            
            case .Left:
                clipRect = CGRect(x: image.size.height - image.size.width / clipRectAspectRatio + imageOffsetInWholeScale.offSetY , y: 0, width: image.size.width / clipRectAspectRatio , height: image.size.width)
            case .Down:
                 clipRect = CGRect(x: 0 , y: image.size.height - image.size.width / clipRectAspectRatio + imageOffsetInWholeScale.offSetY  , width: image.size.width , height: image.size.width / clipRectAspectRatio)
                
            default:
                break
                
                

                
            }
           
        }
        
        if let cgImageAfterCropped = CGImageCreateWithImageInRect(image.CGImage, clipRect){
            return UIImage.init(CGImage: cgImageAfterCropped, scale: image.scale, orientation: image.imageOrientation)
        }
        return nil

    }
    
    

    
    
    
    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
    }
    */

}

extension CropCoverPhotoViewController: UIScrollViewDelegate{
    func scrollViewDidScroll(scrollView: UIScrollView) {
        
        //portrait
        imageOffsetInMinScale.offSetY = (view.bounds.size.height - view.bounds.size.width / StyleSchemeConstant.profileCoverPhotoInfo.aspectRatio) / 2 - scrollView.convertRect(imageViewToBeCropped.frame, toView: nil).origin.y
    }
}
