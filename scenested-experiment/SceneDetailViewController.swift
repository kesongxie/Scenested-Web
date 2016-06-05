//
//  SceneDetailViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/4/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class SceneDetailViewController: UIViewController, UIViewControllerTransitioningDelegate {

    @IBOutlet weak var SceneImageView: UIImageView!
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        print("hello, i did appear")
        
        // Do any additional setup after loading the view.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
    override func preferredStatusBarStyle() -> UIStatusBarStyle {
        return .LightContent
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
