//
//  EditableProfileViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/28/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit
import Photos
import AVFoundation

class EditableProfileViewController: StrechableHeaderViewController {
    private enum UploadPhotoFor{
        case profileAvator
        case profileCover
        case profilePost
        case none
    }
    
    private var imagePickerUploadPhotoFor: UploadPhotoFor = .none
    
    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
  
    // MARK:: Avator, Cover tap gesture configuration
    func tapAvator(sender: UITapGestureRecognizer) {
        let alert = UIAlertController(title: "Change Profile Picture", message: nil, preferredStyle: .ActionSheet)
        let chooseExistingAction = UIAlertAction(title: "Choose from Library", style: .Default, handler: { (action) -> Void in
            self.chooseFromLibarary()
        })
        let takePhotoAction = UIAlertAction(title: "Take Photo", style: .Default, handler:
            {(action) -> Void in
                self.takePhoto()
        })
        
        let cancelAction = UIAlertAction(title: "Cancel", style: .Cancel, handler: {
            (action) -> Void in
            self.finishImagePicker()
        })
        
        alert.addAction(takePhotoAction)
        alert.addAction(chooseExistingAction)
        alert.addAction(cancelAction)
        imagePickerUploadPhotoFor = UploadPhotoFor.profileAvator
        self.presentViewController(alert, animated: true, completion: nil)
    }
    
    
    func tapCover(sender: UITapGestureRecognizer) {
        let alert = UIAlertController(title: "Change Profile Cover", message: nil, preferredStyle: .ActionSheet)
        let chooseExistingAction = UIAlertAction(title: "Choose from Library", style: .Default, handler: { (action) -> Void in
            self.chooseFromLibarary()
        })
        let takePhotoAction = UIAlertAction(title: "Take Photo", style: .Default, handler: {
            (action) -> Void in
            self.takePhoto()
        })
        let cancelAction = UIAlertAction(title: "Cancel", style: .Cancel, handler: nil)
        
        alert.addAction(takePhotoAction)
        alert.addAction(chooseExistingAction)
        alert.addAction(cancelAction)
        imagePickerUploadPhotoFor = UploadPhotoFor.profileCover
        self.presentViewController(alert, animated: true, completion: nil)
    }
    

    
    
    
    // MARK: ImagePicker
    
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
    
    //check whether the App is allowed to get access to the user's camera
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
            return false
        }
    }
    
    
    func finishImagePicker(){
        imagePickerUploadPhotoFor = UploadPhotoFor.none
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

//MARK:: UIImagePickerControllerDelegate and UINavigationControllerDelegate protocol

extension EditableProfileViewController: UIImagePickerControllerDelegate{
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
        default:
            break
        }
    }
    
    func imagePickerControllerDidCancel(picker: UIImagePickerController) {
        self.dismissViewControllerAnimated(true, completion: {
            self.finishImagePicker()
        })
        
    }
}

extension EditableProfileViewController: UINavigationControllerDelegate{
    
}


