//
//  CropAvatorPhotoViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/24/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CropAvatorPhotoViewController: UIViewController {
    private enum SizeMode{
        case landscape
        case portrait
        case square
    }
    
    @IBOutlet weak var scrollView: UIScrollView!
    
    var imageViewToBeCropped = UIImageView()
    
    var image: UIImage?
    
    private var imageOffsetInMinScale = cropImageOffset()
    
    private var imageSizeMode: SizeMode?
    
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
    
    //the ViewController that contains the ImageView that need to be updated
    var cropPhotoForViewController: EditableProfileViewController?
    
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
                imageSizeMode = SizeMode.landscape
                let viewWidth = view.bounds.size.width
                let viewHeight = view.bounds.size.height
                let scaleHeight = view.bounds.size.width
                let scaleWidth = scaleHeight * imageSize.width / imageSize.height
                let frameOriginX = ( viewWidth - scaleWidth ) / 2
                imageViewToBeCropped.frame = CGRect(x: frameOriginX, y: ( viewHeight - viewWidth ) / 2, width: scaleWidth, height: scaleHeight)
                scrollView.contentInset = UIEdgeInsets(top: 0, left: (scaleWidth - scaleHeight) / 2, bottom: 0, right: (scaleWidth - scaleHeight) / 2)
                scrollView.contentSize = CGSize(width: viewWidth, height: scaleHeight)
                scrollView.contentOffset.x = 0
                
            }else if imageSize.width <  imageSize.height{
                //portrait
                imageSizeMode = SizeMode.portrait
                let viewHeight = view.bounds.size.height
                let scaleWidth = view.bounds.size.width
                let scaleHeight = scaleWidth * imageSize.height / imageSize.width
                let frameOriginY = ( viewHeight - scaleHeight ) / 2
                imageViewToBeCropped.frame = CGRect(x: 0, y: frameOriginY, width: scaleWidth, height: scaleHeight)
                scrollView.contentInset = UIEdgeInsets(top: (scaleHeight - scaleWidth) / 2, left: 0, bottom: (scaleHeight - scaleWidth) / 2, right: 0)
                
                scrollView.contentSize = CGSize(width: scaleWidth, height: viewHeight)
                scrollView.contentOffset.y = 0
            }else{
                //square 
                imageSizeMode = SizeMode.square
                let viewHeight = view.bounds.size.height
                let scaleWidth = view.bounds.size.width
                let scaleHeight = scaleWidth
                let frameOriginY = ( viewHeight - scaleHeight ) / 2
                imageViewToBeCropped.frame = CGRect(x: 0, y: frameOriginY, width: scaleWidth, height: scaleHeight)
            }
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
        if let EDVC = cropPhotoForViewController as? EditProfileViewController{
            EDVC.dismissViewControllerAnimated(true, completion: nil)
            if let imageAfterCropped = cropSquareImage(image!){
                EDVC.profileAvator.image = imageAfterCropped
                EDVC.finishImagePicker()
                //Doent save imageAfterCropped to the server yet, wait until the user hit the saveBtn on the Edit Profile scene
            }
        }else if let PVC = cropPhotoForViewController as? ProfileViewController{
            PVC.dismissViewControllerAnimated(true, completion: nil)
            if let imageAfterCropped = cropSquareImage(image!){
                PVC.profileAvator.image = imageAfterCropped
                PVC.finishImagePicker()
                //save imageAfterCropped to the server
            }

        }
    }
    
    
    
    
    private func cropSquareImage(image: UIImage) -> UIImage?{
        var clipRect: CGRect = CGRectZero
        var clipSquareWidth: CGFloat
        if let mode = imageSizeMode{
            switch mode{
            case .portrait:
                clipSquareWidth = image.size.width

                if image.imageOrientation == .Right{
                    clipRect = CGRect(x: imageOffsetInWholeScale.offSetY , y: 0, width: clipSquareWidth, height: clipSquareWidth)
                }else if image.imageOrientation == .Left{
                     clipRect = CGRect(x: image.size.height - clipSquareWidth - imageOffsetInWholeScale.offSetY , y: 0, width: clipSquareWidth, height: clipSquareWidth)
                }else if image.imageOrientation == .Down{
                    clipRect = CGRect(x: 0, y: image.size.height - clipSquareWidth - imageOffsetInWholeScale.offSetY , width: clipSquareWidth, height: clipSquareWidth)
                }else{
                     clipRect = CGRect(x: 0 , y: imageOffsetInWholeScale.offSetY , width: clipSquareWidth, height: clipSquareWidth)
                }
            case .square:
                clipSquareWidth = image.size.width
                clipRect = CGRect(x: 0 , y: 0 , width: clipSquareWidth, height: clipSquareWidth)
            case .landscape:
                clipSquareWidth = image.size.height
                if image.imageOrientation == .Right{
                    clipRect = CGRect(x: 0, y: image.size.width - clipSquareWidth - imageOffsetInWholeScale.offSetX, width: clipSquareWidth, height: clipSquareWidth)
                }else if image.imageOrientation == .Left{
                    clipRect = CGRect(x: 0, y: imageOffsetInWholeScale.offSetX, width: clipSquareWidth, height: clipSquareWidth)
                }else if image.imageOrientation == .Down{
                    print( image.size.width - clipSquareWidth - imageOffsetInWholeScale.offSetX)
                    clipRect = CGRect(x: image.size.width - clipSquareWidth - imageOffsetInWholeScale.offSetX, y: 0, width: clipSquareWidth, height: clipSquareWidth)
                }else{
                    clipRect = CGRect(x: imageOffsetInWholeScale.offSetX, y: 0, width: clipSquareWidth, height: clipSquareWidth)
                }
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

extension CropAvatorPhotoViewController: UIScrollViewDelegate{
    func scrollViewDidScroll(scrollView: UIScrollView) {
       // imageViewToBeCropped.frame
        //portraint
        if imageSizeMode == SizeMode.portrait{
             imageOffsetInMinScale.offSetY = (view.bounds.size.height - view.bounds.size.width) / 2 - scrollView.convertRect(imageViewToBeCropped.frame, toView: nil).origin.y
        }else if imageSizeMode == SizeMode.landscape{
            imageOffsetInMinScale.offSetX = -scrollView.convertRect(imageViewToBeCropped.frame, toView: nil).origin.x
            
        }
        
       
    }
}

