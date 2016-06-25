//
//  CropAvatorPhotoViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/24/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CropAvatorPhotoViewController: UIViewController {
    private enum sizeMode{
        case landscape
        case portrait
        case square
    }
    
    private struct cropImageOffset{
        var offSetX: CGFloat = 0
        var offSetY: CGFloat = 0
    }
    
    private struct originalImageInfo{
        var imageoOrientation: UIImageOrientation = .Up
        var scale: CGFloat = 0
    }

    
    @IBOutlet weak var scrollView: UIScrollView!
    
    
    
    var imageViewToBeCropped = UIImageView()
    
    var image: UIImage?
    
    
    
    private var imageOffsetInMinScale = cropImageOffset()
    
    //store the orientation and scale information before the image being cropped so that the corresponding info is preserved even after cropped
    private var imageInfomationBeforeCrop = originalImageInfo()

    private var imageSizeMode: sizeMode?
    
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
        scrollView.delegate = self
        
        // Do any additional setup after loading the view.
        if let imageToBeCropped = image{
            imageViewToBeCropped.image = imageToBeCropped
            imageViewToBeCropped.contentMode = .ScaleAspectFill
            let imageSize = imageToBeCropped.size
            
            if imageSize.width > imageSize.height{
                //landscape
                imageSizeMode = sizeMode.landscape
                let viewWidth = view.bounds.size.width
                let viewHeight = view.bounds.size.height
                let scaleHeight = view.bounds.size.width
                let scaleWidth = scaleHeight * imageSize.width / imageSize.height
                let frameOriginX = ( viewWidth - scaleWidth ) / 2
                imageViewToBeCropped.frame = CGRect(x: frameOriginX, y: ( viewHeight - viewWidth ) / 2, width: scaleWidth, height: scaleHeight)
                scrollView.contentInset = UIEdgeInsets(top: 0, left: (scaleWidth - scaleHeight) / 2, bottom: 0, right: (scaleWidth - scaleHeight) / 2)
                scrollView.contentSize = CGSize(width: viewWidth, height: scaleHeight)
                
            }else if imageSize.width <  imageSize.height{
                //portrait
                imageSizeMode = sizeMode.portrait
                let viewHeight = view.bounds.size.height
                let scaleWidth = view.bounds.size.width
                let scaleHeight = scaleWidth * imageSize.height / imageSize.width
                let frameOriginY = ( viewHeight - scaleHeight ) / 2
                imageViewToBeCropped.frame = CGRect(x: 0, y: frameOriginY, width: scaleWidth, height: scaleHeight)
                scrollView.contentInset = UIEdgeInsets(top: (scaleHeight - scaleWidth) / 2, left: 0, bottom: (scaleHeight - scaleWidth) / 2, right: 0)
                
                scrollView.contentSize = CGSize(width: scaleWidth, height: viewHeight)
                scrollView.contentOffset.y += (scaleHeight - scaleWidth) / 2
            }else{
                //square 
                imageSizeMode = sizeMode.square
            }
            view.addSubview(imageViewToBeCropped)
            scrollView.addSubview(imageViewToBeCropped)
            if let cropAvatorView = view as? CropAvatorView{
                cropAvatorView.cancelBtn.addTarget(self, action: #selector(CropAvatorPhotoViewController.cancelBtnTapped), forControlEvents: .TouchUpInside)
                cropAvatorView.doneBtn.addTarget(self, action: #selector(CropAvatorPhotoViewController.doneBtnTapped), forControlEvents: .TouchUpInside)
                
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
                
                if let orientation = image?.imageOrientation{
                    imageInfomationBeforeCrop.imageoOrientation = orientation
                }
                
                if let imageAfterCropped = cropSquareImage(image!){
                    profileVC.profileAvator.image = imageAfterCropped
                    print(profileVC.profileAvator.image)
                }
            }
            
        }

    }
    
    
    private func cropSquareImage(image: UIImage) -> UIImage?{
        var clipRect: CGRect = CGRectZero
        let clipSquareWidth = image.size.width
        if let mode = imageSizeMode{
            switch mode{
            case .portrait:
                clipRect = CGRect(x: 0, y: imageOffsetInWholeScale.offSetY , width: clipSquareWidth, height: clipSquareWidth)
            default:break
            }
        }
        
        if let cgImageAfterCropped = CGImageCreateWithImageInRect(image.CGImage, clipRect){
            return UIImage.init(CGImage: cgImageAfterCropped, scale: imageInfomationBeforeCrop.scale, orientation: imageInfomationBeforeCrop.imageoOrientation)
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

extension CropAvatorPhotoViewController: UIScrollViewDelegate{
    func scrollViewDidScroll(scrollView: UIScrollView) {
       // imageViewToBeCropped.frame
        //portraint
       imageOffsetInMinScale.offSetY = (view.bounds.size.height - view.bounds.size.width) / 2 - scrollView.convertRect(imageViewToBeCropped.frame, toView: nil).origin.y
    }
}

