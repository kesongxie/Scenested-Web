//
//  UIButtonExtension.swift
//  Scenested
//
//  Created by Xie kesong on 4/8/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

extension UIButton{
    func becomeFollowButton(){
        self.layer.borderColor = StyleSchemeConstant.followBtn.borderColor
        self.layer.borderWidth = StyleSchemeConstant.followBtn.borderWidth
        self.layer.cornerRadius = StyleSchemeConstant.followBtn.cornerRadius
        self.backgroundColor = StyleSchemeConstant.followBtn.backgroundColor
        self.setTitleShadowColor(StyleSchemeConstant.followBtn.shadowColor, forState: .Normal)
        self.setTitleColor(StyleSchemeConstant.followBtn.titleColor, forState: .Normal)
        self.titleLabel?.font = StyleSchemeConstant.followBtn.font
        self.setTitle("＋FOLLOW", forState: .Normal)
    }
    
    func becomeEditProfileButton(){
        self.layer.borderColor = StyleSchemeConstant.editProfileBtn.borderColor
        self.layer.borderWidth = StyleSchemeConstant.editProfileBtn.borderWidth
        self.layer.cornerRadius = StyleSchemeConstant.editProfileBtn.cornerRadius
        self.backgroundColor = StyleSchemeConstant.editProfileBtn.backgroundColor
        self.setTitleColor(StyleSchemeConstant.editProfileBtn.titleColor, forState: .Normal)
        self.titleLabel?.font = StyleSchemeConstant.editProfileBtn.font
        self.setTitle("Edit Profile", forState: .Normal)
    }
    
}
