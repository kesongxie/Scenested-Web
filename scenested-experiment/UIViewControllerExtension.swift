//
//  UIViewControllerExtension.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/17/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

extension UIViewController{
    func dismissKeyBoardWhenTapped(){
        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(UIViewController.dismissKeyboard))
        view.addGestureRecognizer(tapGesture)
    }
    
    func dismissKeyboard(){
        view.endEditing(true)
    }
}
