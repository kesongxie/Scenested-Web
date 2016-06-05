//
//  UIButtonExtension.swift
//  Scenested
//
//  Created by Xie kesong on 4/8/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

extension UIButton{
    func becomeMagentaButton(){
        self.layer.borderColor = StyleSchemeConstant.ThemeColor.CGColor
        self.layer.borderWidth = StyleSchemeConstant.buttonStyle.borderWidth
        self.layer.cornerRadius = StyleSchemeConstant.buttonStyle.buttonCornerRadius
        self.setTitleColor(StyleSchemeConstant.ThemeColor, forState: .Normal)
    }
    
}
