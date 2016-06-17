//
//  EditProfileViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/17/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class EditProfileViewController: UIViewController {
   
    
    @IBOutlet weak var scrollView: UIScrollView!
    
    @IBOutlet weak var avatorImageView: UIImageView!
    
    @IBOutlet weak var coverImageView: UIImageView!
    
    @IBOutlet weak var coverHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var nameTextField: UITextField!
    
    
    @IBOutlet weak var bioTextView: UITextView!
    
    
    private var profileCoverHeight: CGFloat = 0

    private var isKeyBoardActive = false
    
    
    
    
    
    override func viewDidLoad() {
        scrollView.delegate = self
        self.dismissKeyBoardWhenTapped()
        
        self.navigationController?.navigationBar.backgroundColor = StyleSchemeConstant.navigationBarStyle.backgroundColor
        self.navigationController?.navigationBar.translucent = StyleSchemeConstant.navigationBarStyle.translucent
        scrollView.alwaysBounceVertical = true
        updateAvator()
        updateCover()
    }
    
    
    override func viewDidAppear(animated: Bool) {
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(EditProfileViewController.keyboardDidShow), name: UIKeyboardDidShowNotification, object: nil)
        
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(EditProfileViewController.keyBoardWillHide), name: UIKeyboardWillHideNotification, object: nil)
    }
    
    
    func updateAvator(){
        avatorImageView.becomeCircleAvator()
    }
    
    
    func updateCover(){
        if let coverImageSize = coverImageView.image?.size{
            profileCoverHeight =  UIScreen.mainScreen().bounds.size.width * coverImageSize.height / coverImageSize.width
            coverHeightConstraint.constant = profileCoverHeight
        }
    }
    
    func strechCover(){
        if scrollView.contentOffset.y < 0 {
            var coverHeaderRect = CGRect(x: 0, y: 0, width: coverImageView.bounds.width, height: profileCoverHeight)
            let caculatedHeight = profileCoverHeight - scrollView.contentOffset.y
            let caculatedOrigin = scrollView.contentOffset.y
            coverHeaderRect.size.height = caculatedHeight
            coverHeaderRect.origin.y  = caculatedOrigin
            coverImageView.frame = coverHeaderRect
        }
    }
    
    
    func keyboardDidShow(notification: NSNotification){
        if let keyboardSize = (notification.userInfo?["UIKeyboardFrameBeginUserInfoKey"] as? NSValue)?.CGRectValue(){
                scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 120, right: 0)
            
            UIView.animateWithDuration(0.3, animations: {
                self.scrollView.contentOffset.y = 120
                }, completion: { finished in
                    self.isKeyBoardActive = true
                })
            
        }
    }
    
    func keyBoardWillHide(notification: NSNotification){
         scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)
         isKeyBoardActive = false
    }
    
    
    
    
    
}

extension EditProfileViewController: UIScrollViewDelegate{
    func scrollViewDidScroll(scrollView: UIScrollView) {
        strechCover()
        if isKeyBoardActive{
            view.endEditing(true)
        }
    }
}


