//
//  ThemeViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/19/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit


let reusedIden = "SceneCell"

class ThemeViewController: UITableViewController{
    
    @IBAction func backButtonTapped(sender: UIBarButtonItem) {
        self.navigationController?.popViewControllerAnimated(true)
    }
    var themeName: String?
    
    var themeScene:[Scene]?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.tableView.estimatedRowHeight = self.tableView.rowHeight
        self.tableView.rowHeight = UITableViewAutomaticDimension

        if themeName != nil{
            self.title = themeName
        }
    }
    
    
    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        return 1
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return themeScene!.count
    }
    
    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let sceneCell = tableView.dequeueReusableCellWithIdentifier(reusedIden, forIndexPath: indexPath) as! SceneTableViewCell
        sceneCell.scenePictureUrl = themeScene![indexPath.row].imageUrl
        sceneCell.themeName = themeScene![indexPath.row].themeName
        
        sceneCell.descriptionText = themeScene![indexPath.row].postText
        sceneCell.postUserName = themeScene![indexPath.row].postUserName
        sceneCell.postTimeText = themeScene![indexPath.row].postTime
        
               
        //tableView.setNeedsLayout()
        return sceneCell
    }
    
    
    
    
    
}


//class ThemeViewController: EditableProfileViewController {
//    
//    @IBOutlet weak var globalView: UITableView!
//    
//    @IBOutlet weak var themeCoverImageView: UIImageView!
//    
//    @IBOutlet weak var themeCoverHeightConstraint: NSLayoutConstraint!
//    
//    
//    
//    var themeImage: UIImage?
//    var themeName: String?
//    private var themeCoverHeight: CGFloat = 0
//    private var headerHeightOffset: CGFloat = 0 // make the cover's height little bit larger than the original screen height
//    private var themeCoverOriginalScreenHeight: CGFloat = 0
//
//    
//    private let initialContentOffsetTop: CGFloat = 64.0
//
//    
//    
//
////    @IBOutlet weak var avator: UIImageView!{
////        didSet{
////            avator.becomeCircleAvator()
////        }
////    }
////    @IBOutlet weak var connection: UIImageView!{
////        didSet{
////            connection.layer.cornerRadius = connection.frame.size.width / 2
////            connection.clipsToBounds = true
////        }
////    }
////    @IBOutlet weak var connection2: UIImageView!{
////        didSet{
////            connection2.layer.cornerRadius = connection2.frame.size.width / 2
////            connection2.clipsToBounds = true
////        }
////    }
//
//    override func viewDidLoad() {
//        super.viewDidLoad()
//        
//        if themeName != nil{
//            self.title = themeName
//        }
//        if themeImage != nil{
//            themeCoverImageView.image = themeImage
//            if let coverImageSize = themeCoverImageView.image?.size{
//                themeCoverOriginalScreenHeight =  UIScreen.mainScreen().bounds.size.width * coverImageSize.height / coverImageSize.width
//                themeCoverHeight = themeCoverOriginalScreenHeight + headerHeightOffset
//                themeCoverHeightConstraint.constant = themeCoverHeight
//            }
//        }
//
//        // Do any additional setup after loading the view.
//    }
//    
//    
//    
//    
//    
//    override func viewDidAppear(animated: Bool) {
//        //stretchy header set up
//        self.globalScrollView = globalView
//        self.coverImageView = themeCoverImageView
//        self.coverHeight = themeCoverImageView.bounds.size.height
//        self.defaultInitialContentOffsetTop = initialContentOffsetTop
//        self.stretchWhenContentOffsetLessThanZero = true
//        
//    }
//
//    
//    
//    
//    
//
//    override func didReceiveMemoryWarning() {
//        super.didReceiveMemoryWarning()
//        // Dispose of any resources that can be recreated.
//    }
//    
//
//    
//    // MARK: - Navigation
//
//    
////    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
////        print("hello")
//////         Get the new view controller using segue.destinationViewController.
//////         Pass the selected object to the new view controller.
////    }
// 
//
//}
