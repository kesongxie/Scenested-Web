//
//  ProfileNavigationController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 4/21/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class ProfileNavigationController: UINavigationController {

    override func viewDidLoad() {
        super.viewDidLoad()
        self.navigationBar.titleTextAttributes = StyleSchemeConstant.navigationBarStyle.titleTextAttributes
        self.navigationBar.translucent = StyleSchemeConstant.navigationBarStyle.translucent
        
        
//        self.navigationBar.barTintColor = UIColor(red: 60 / 255.0, green: 60 / 255.0, blue: 60 / 255.0, alpha: 1)
        // Do any additional setup after loading the view.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
//    override func preferredStatusBarStyle() -> UIStatusBarStyle {
//        return .LightContent
//    }

    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
    }
    */

}
