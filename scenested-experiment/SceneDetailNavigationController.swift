//
//  SceneDetailNavigationController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/9/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class SceneDetailNavigationController: UINavigationController {
    override func viewDidLoad() {
//        self.navigationBar.titleTextAttributes = [NSFontAttributeName: UIFont.boldSystemFontOfSize(15), NSForegroundColorAttributeName: StyleSchemeConstant.ThemeColor]
        
        self.navigationBar.titleTextAttributes = [NSFontAttributeName: UIFont.boldSystemFontOfSize(15)]

//        self.navigationBar.tintColor = StyleSchemeConstant.ThemeColor
        self.navigationBar.barTintColor = UIColor.whiteColor()
        
//        self.navigationBar.barTintColor = StyleSchemeConstant.ThemeColor
    }
    
    
    
    
}
