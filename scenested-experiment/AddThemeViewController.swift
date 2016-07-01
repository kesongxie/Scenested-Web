//
//  AddThemeViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/21/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class AddThemeViewController: StrechableHeaderViewController {
    
    @IBOutlet weak var scrollView: UIScrollView!
    
    @IBOutlet weak var themeCoverImageView: UIImageView!
    
    @IBOutlet weak var themeNameTextField: UITextField!
    
    private var profileCoverHeight: CGFloat = 0
    
    private var isKeyBoardActive: Bool = false
    
    private var bottomInsetWhenKeyboardShows:CGFloat = 110
    
    private var themeCoverAspectRatio:CGFloat = 1.2
    
    private var headerStretchingEffectEnable: Bool = true
    
    @IBOutlet weak var themeCoverHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var cameraIcon: UIImageView!
    
    
    @IBAction func themeCoverTapped(sender: UITapGestureRecognizer) {
        let alert = UIAlertController(title: "Add Cover for Theme", message: nil, preferredStyle: .ActionSheet)
        
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
        imagePickerUploadPhotoFor = UploadPhotoFor.themeCover
        self.presentViewController(alert, animated: true, completion: nil)
        
    }
    var themeCoverImage: UIImage?{
        didSet{
            themeCoverImageView?.image = themeCoverImage
            cameraIcon?.hidden = true
            print(themeCoverImage)

        }
    }
    
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        themeNameTextField.delegate = self
        self.navigationItem.rightBarButtonItem?.setTitleTextAttributes([NSFontAttributeName: UIFont.systemFontOfSize(17, weight: UIFontWeightMedium) ] , forState: .Normal)

        self.automaticallyAdjustsScrollViewInsets = false
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(AddThemeViewController.keyboardDidShow), name: UIKeyboardDidShowNotification, object: nil)
        
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(AddThemeViewController.keyBoardDidHide), name: UIKeyboardDidHideNotification, object: nil)
        
        themeCoverHeightConstraint.constant = view.bounds.size.width / themeCoverAspectRatio
        
        if themeCoverImage != nil{
            themeCoverImageView.image = themeCoverImage
            cameraIcon.hidden = true
        }
    }
    
    override func viewDidAppear(animated: Bool) {
        //stretchy header set up
        self.globalScrollView = scrollView
        self.coverImageView = themeCoverImageView
        self.coverHeight =  themeCoverHeightConstraint.constant
        if themeCoverImage != nil{
            themeNameTextField.becomeFirstResponder()
        }
    }
    
    
    
    override func scrollViewDidScroll(scrollView: UIScrollView) {
        if isKeyBoardActive{
            view.endEditing(true)
        }
//        if headerStretchingEffectEnable{
            //super.scrollViewDidScroll(scrollView)
        //}
        
    }
    
    func keyboardDidShow(notification: NSNotification){
        scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: bottomInsetWhenKeyboardShows, right: 0)
        UIView.animateWithDuration(0.3, animations: {
            self.scrollView.contentOffset.y = self.bottomInsetWhenKeyboardShows
            }, completion: { finished in
                self.isKeyBoardActive = true
        })
    }
    
    func keyBoardDidHide(notifcation: NSNotification){

        UIView.animateWithDuration(0.3, animations: {
            self.scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)
            }, completion: { finished in
                self.isKeyBoardActive = false
//                self.headerStretchingEffectEnable = true
        })
    }
    
   
    
}

extension AddThemeViewController: UITextFieldDelegate{
//    func textFieldDidBeginEditing(textField: UITextField) {
//        self.headerStretchingEffectEnable = false
//    }
//    
//    func textFieldDidEndEditing(textField: UITextField) {
//        self.headerStretchingEffectEnable = true
//    }
}
