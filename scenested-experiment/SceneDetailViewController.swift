//
//  SceneDetailViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/4/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class SceneDetailViewController: UIViewController, UIViewControllerTransitioningDelegate {

    @IBOutlet weak var sceneImageView: UIImageView!
   
    @IBOutlet weak var sceneImageViewHeightConstraint: NSLayoutConstraint!
   
    var scene: Scene?

    
    override func viewDidLoad() {
        super.viewDidLoad()
        renderSceneImage()
      
        // Do any additional setup after loading the view.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    
    override func prefersStatusBarHidden() -> Bool {
        return true
    }
    
    func renderSceneImage() -> Void{
        if scene != nil{
            sceneImageView?.image = UIImage(named: scene!.imageUrl)
            if let sceneImage = sceneImageView?.image?.size{
                let sceneImageAspectRatio: CGFloat = sceneImage.width / sceneImage.height
                sceneImageView.frame.size.width = view.bounds.width
                sceneImageView.frame.size.height = sceneImageView.frame.size.width / sceneImageAspectRatio
                sceneImageViewHeightConstraint.constant = sceneImageView.frame.size.height
            }
        }
    }

    
    @IBAction func dismissDetailView(sender: UITapGestureRecognizer) {
        presentingViewController?.dismissViewControllerAnimated(true, completion: nil)
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
