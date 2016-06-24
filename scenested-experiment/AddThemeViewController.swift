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
    
    private var bottomInsetWhenKeyboardShows:CGFloat = 160
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        self.navigationItem.rightBarButtonItem?.setTitleTextAttributes([NSFontAttributeName: UIFont.systemFontOfSize(17, weight: UIFontWeightMedium) ] , forState: .Normal)

        self.automaticallyAdjustsScrollViewInsets = false
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(AddThemeViewController.keyboardDidShow), name: UIKeyboardDidShowNotification, object: nil)
        
        NSNotificationCenter.defaultCenter().addObserver(self, selector: #selector(AddThemeViewController.keyBoardDidHide), name: UIKeyboardDidHideNotification, object: nil)
    }
    
    override func viewDidAppear(animated: Bool) {
        //stretchy header set up
        self.globalScrollView = scrollView
        self.coverImageView = themeCoverImageView
        self.coverHeight = themeCoverImageView.bounds.size.height
    }
    
    override func scrollViewDidScroll(scrollView: UIScrollView) {
        super.scrollViewDidScroll(scrollView)
        if isKeyBoardActive{
            view.endEditing(true)
        }
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
        
//        print(scrollView.contentOffset)

        UIView.animateWithDuration(0.3, animations: {
            self.scrollView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)
            }, completion: { finished in
                self.isKeyBoardActive = false
        })
    }
    
   
    
}
