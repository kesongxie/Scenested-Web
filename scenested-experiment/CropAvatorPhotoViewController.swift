//
//  CropAvatorPhotoViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/24/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CropAvatorPhotoViewController: UIViewController {

    @IBOutlet weak var scrollView: UIScrollView!
    
    
    
    @IBOutlet weak var imageToBeCropped: UIImageView!
    
    @IBOutlet weak var heightConstraint: NSLayoutConstraint!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        scrollView.contentSize = CGSize(width: 320, height: 1000)
     
    }
    
    
    override func viewDidLayoutSubviews() {
//        imageToBeCropped.frame.size.width = view.bounds.size.width
        
        
        print(imageToBeCropped.frame)
        print(scrollView.contentOffset)
        print(scrollView.frame)
        print(scrollView.contentSize)

    }
    
    
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
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
