//
//  MediaResponseViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/1/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit
import Photos
import AVFoundation

class MediaResponseViewController: UIViewController {

    
    var imagePickerUploadPhotoFor: UploadPhotoFor = .none

    
    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func chooseFromLibarary(){
        if isAvailabeToPickFromLibrary(){
            let savedAlbumSource = UIImagePickerControllerSourceType.SavedPhotosAlbum
            if !UIImagePickerController.isSourceTypeAvailable(savedAlbumSource){
                //the given source type is not availabe
                return
            }
            //once given source type is available, check which media type is available
            if UIImagePickerController.availableMediaTypesForSourceType(savedAlbumSource) != nil{
                let imagePicker = UIImagePickerController()
                imagePicker.delegate = self
                imagePicker.mediaTypes = ["public.image"] //only supports static image
                self.presentViewController(imagePicker, animated: true, completion: nil)
            }
        }else{
            //make sure the setting is good
        }
    }
    
    //check whether the App is allowed to get access to the user's photo libarary, ask for authorization, otherwise
    func isAvailabeToPickFromLibrary() -> Bool{
        let status = PHPhotoLibrary.authorizationStatus()
        switch status {
        case .Authorized:
            return true
        case .NotDetermined:
            PHPhotoLibrary.requestAuthorization(){PHAuthorizationStatus -> Void  in}
            return false
        case .Restricted:
            return false
        case .Denied:
            let alert = UIAlertController(title: "Authorization Needed", message: "Authorization needed in order to choose photo from libarary", preferredStyle: .Alert)
            let dontAllowAction = UIAlertAction(title: "Don't Allow", style: .Default, handler: nil)
            let goToSettingAction = UIAlertAction(title: "Ok", style: .Default, handler: {
                _ in
                if let url = NSURL(string: UIApplicationOpenSettingsURLString){
                    UIApplication.sharedApplication().openURL(url)
                }
            })
            alert.addAction(dontAllowAction)
            alert.addAction(goToSettingAction)
            self.presentViewController(alert, animated: true, completion: nil)
            return false
        }
    }
    
    
    
    func takePhoto(){
        if isAvailableToUseCamera(){
            let camera = UIImagePickerControllerSourceType.Camera
            if !UIImagePickerController.isSourceTypeAvailable(camera){
                return
            }
            //the camera is available
            if UIImagePickerController.availableMediaTypesForSourceType(camera) != nil{
                let imagePicker = UIImagePickerController()
                imagePicker.sourceType = .Camera
                imagePicker.delegate = self
                imagePicker.mediaTypes = ["public.image"]
                self.presentViewController(imagePicker, animated: true, completion: nil)
                
            }
        }else{
            //make sure the setting is good
        }
    }
    
    
    func isAvailableToUseCamera() -> Bool{
        let status = AVCaptureDevice.authorizationStatusForMediaType(AVMediaTypeVideo)
        print(status)
        switch status {
        case .Authorized:
            return true
        case .NotDetermined:
            AVCaptureDevice.requestAccessForMediaType(AVMediaTypeVideo, completionHandler: nil)
            return false
        case .Restricted:
            return false
        case .Denied:
            dispatch_async(dispatch_get_main_queue(), {
                let alert = UIAlertController(title: "Authorization Needed", message: "Authorization needed in order to use the camera to capture a picture", preferredStyle: .Alert)
                
                let dontAllowAction = UIAlertAction(title: "Don't Allow", style: .Default, handler: nil)
                let goToSettingAction = UIAlertAction(title: "Ok", style: .Default, handler: {
                    _ in
                    if let url = NSURL(string: UIApplicationOpenSettingsURLString){
                        UIApplication.sharedApplication().openURL(url)
                    }
                })
                alert.addAction(dontAllowAction)
                alert.addAction(goToSettingAction)
                self.presentViewController(alert, animated: true, completion: nil)
            })
            return false
        }
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


extension MediaResponseViewController: UIImagePickerControllerDelegate{
    func imagePickerController(picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : AnyObject]) {
        
        let selectedImage = info[UIImagePickerControllerOriginalImage] as! UIImage
        
        //depends on which type the image is, crop avator or cover
        
        
        switch imagePickerUploadPhotoFor{
        case .profileAvator:
            if let cropAvatorViewController = storyboard?.instantiateViewControllerWithIdentifier("CropAvatorPhotoViewControllerIden") as? CropAvatorPhotoViewController
                
                
            {
                cropAvatorViewController.image = selectedImage
                cropAvatorViewController.cropPhotoForViewController = self
                picker.presentViewController(cropAvatorViewController, animated: true, completion: {
                })
            }
        case .profileCover:
            if let cropCoverViewController = storyboard?.instantiateViewControllerWithIdentifier("CropCoverPhotoViewControllerIden") as? CropCoverPhotoViewController
            {
                cropCoverViewController.image = selectedImage
                cropCoverViewController.cropPhotoForViewController = self
                picker.presentViewController(cropCoverViewController, animated: true, completion: {
                })
            }
        case .themeCover:
            
            if let addThemeNaviVC = storyboard?.instantiateViewControllerWithIdentifier("AddThemeNaviIden") as? AddThemeNavigationController
            {
                if let addThemeVC = addThemeNaviVC.viewControllers.first as? AddThemeViewController{
                    addThemeVC.themeCoverImage = selectedImage
                    picker.presentViewController(addThemeNaviVC, animated: true, completion: {
                    })
                }
            }
            
            
            
        default:
            break
        }
    }
    
    func imagePickerControllerDidCancel(picker: UIImagePickerController) {
        self.dismissViewControllerAnimated(true, completion: {
            self.finishImagePicker()
        })
        
    }
    
    
    func finishImagePicker(){
        imagePickerUploadPhotoFor = UploadPhotoFor.none
    }
}

extension MediaResponseViewController: UINavigationControllerDelegate{
    
}






