//
//  EditProfileViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/17/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class EditProfileViewController: EditableProfileViewController {
    @IBOutlet weak var scrollView: UIScrollView!
    
    @IBOutlet weak var profileAvator: UIImageView!
    
    @IBOutlet weak var profileCover: UIImageView!
    
    @IBOutlet weak var coverHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var nameTextField: UITextField!
    
    @IBOutlet weak var bioTextView: UITextView!

    private var isKeyBoardActive = false
    
    private let bottomInsetWhenKeyboardShows:CGFloat = 120
    
    override func viewDidLoad() {
        self.navigationItem.rightBarButtonItem?.setTitleTextAttributes([NSFontAttributeName: UIFont.systemFontOfSize(17, weight: UIFontWeightMedium) ] , forState: .Normal)
        
        self.dismissKeyBoardWhenTapped()
        self.navigationController?.navigationBar.backgroundColor = StyleSchemeConstant.navigationBarStyle.backgroundColor
        self.navigationController?.navigationBar.translucent = StyleSchemeConstant.navigationBarStyle.translucent
        updateAvator()
        updateCover()
        addTapGestureForAvator()
        addTapGestureForCover()
    }
    
    
    
    
    override func viewDidAppear(animated: Bool) {
        //strechy header set up
        self.globalScrollView = scrollView
        self.coverImageView = profileCover
        self.coverHeight = profileCover.bounds.size.height
        self.stretchWhenContentOffsetLessThanZero = true
        
        //keyboard set up
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(EditProfileViewController.keyboardDidShow), name: UIKeyboardDidShowNotification, object: nil)
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(EditProfileViewController.keyBoardWillHide), name: UIKeyboardWillHideNotification, object: nil)
    }
    
    func updateAvator(){
        profileAvator.becomeCircleAvator()
    }
    
    func updateCover(){
        if let coverImageSize = profileCover.image?.size{
            coverHeightConstraint.constant =  UIScreen.mainScreen().bounds.size.width * coverImageSize.height / coverImageSize.width
        }
    }
    
    func addTapGestureForAvator(){
        let tap = UITapGestureRecognizer(target: self, action: #selector(EditProfileViewController.tapAvator))
        profileAvator.addGestureRecognizer(tap)
    }
    
    func addTapGestureForCover(){
        let tap = UITapGestureRecognizer(target: self, action: #selector(EditProfileViewController.tapCover))
        profileCover.addGestureRecognizer(tap)
    }
    
    
    
    
    
    


    func keyboardDidShow(notification: NSNotification){
//        if let keyboardSize = (notification.userInfo?["UIKeyboardFrameBeginUserInfoKey"] as? NSValue)?.CGRectValue(){
            scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: bottomInsetWhenKeyboardShows, right: 0)
            
            UIView.animateWithDuration(0.3, animations: {
                self.scrollView.contentOffset.y = 120
                }, completion: { finished in
                    self.isKeyBoardActive = true
                })
            
       // }
    }
    
    func keyBoardWillHide(notification: NSNotification){
         scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)
         isKeyBoardActive = false
    }
    
    override func scrollViewDidScroll(scrollView: UIScrollView) {
        super.scrollViewDidScroll(scrollView)
        if isKeyBoardActive{
            view.endEditing(true)
        }
    }
}
