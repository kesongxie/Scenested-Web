//
//  ProfileViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 4/10/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class ProfileViewController: UIViewController {

    
    @IBOutlet weak var profileCover: UIImageView!
    
    @IBOutlet weak var profileCoverHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var themeSlideHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var profileAvator: UIImageView!
    
    @IBOutlet weak var profileFollowButton: UIButton!
    
    
    @IBOutlet weak var themesCollectionView: UICollectionView!
    
    
    @IBOutlet weak var postTableView: UITableView!
    
    @IBOutlet weak var tableHeaderView: UIView!

    
    
    private var profileCoverHeight: CGFloat = 0
    private var headerHeightOffset: CGFloat = 34 // make the cover's height little bit larger than the original screen height
    private var profileCoverOriginalScreenHeight: CGFloat = 0
    

    
    private var themeImageSize: CGSize = CGSizeZero //the size of the individual theme UIImageView
    
    
    
    private let closeUpTransition = CloseUpAnimator()
    
    
    
    
    
    /* define the style constant for the theme slide  */
    private struct themeSlideConstant{
        struct sectionEdgeInset{
            static let top:CGFloat = 4
            static let left:CGFloat = 16
            static let bottom:CGFloat = 4
            static let right:CGFloat = 16
        }
        
        //the space between each item
        static let lineSpace: CGFloat = 6
        static let maxVisibleThemeCount: CGFloat = 2.2 //the max number of theme that is allowed to display at the screen
        static let themeImageAspectRatio:CGFloat = 3 / 4
        static let precicitionOffset: CGFloat = 1 //prevent the height of the collectionView from less than the total of the cell height and inset during the calculation
        static let themeCellReuseIdentifier: String = "themeCell"
    }
    
    
    //themes data source
    //let themeNames: [String] = ["This is my first coustic fingerstyle guitar concert in New York", "Glad to see this year US Open Final", "My first hackathon ever!"]
    let themeNames: [String] = ["GUITAR", "TENNIS", "PROGRAMMING"]

    
    let themeImages: [String] = ["theme1", "theme2", "thumb_2"]
    
    
    
    struct Posts{
        let id: Int
        let imageUrl: String
        let themeName: String
        let postText: String
        let postDate: String
    }
    
    static let post1 = Posts(id: 1, imageUrl: "cover3", themeName: "TENNIS", postText: "Great to be able to experience this year's #USOpen", postDate: "Sep 4, 2015")
    static let post2 = Posts(id: 3, imageUrl: "thumb_2", themeName: "PROGRAMMING", postText: "This is my first hackathon at Lehman Collge", postDate: "May 02, 2015")

    static let post3 = Posts(id: 2, imageUrl: "thumb_1", themeName: "GUITAR", postText: "This is my first time to see a live acoustic guitar concert since I picked up guitar about five years ago. #TraceBundy", postDate: "May 17, 2014")

    
  
    

    
    
    
    
    //post data source
    
    var posts = [post1, post2, post3]
    //var posts = [post1, post2, post3, post1, post2, post3, post1, post2, post3, post1, post2, post3, post1, post2, post3, post1, post2, post3, post1, post2, post3, post1, post2, post3]
    
    
    
    
    
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        themesCollectionView.delegate = self
        themesCollectionView.dataSource = self
        postTableView.delegate = self
        postTableView.dataSource = self
        
        
        postTableView.alwaysBounceVertical = true
        //        self.automaticallyAdjustsScrollViewInsets = false
        
        profileCover.image = UIImage(named: "cover3")
        if let coverImageSize = profileCover.image?.size{
            profileCoverOriginalScreenHeight =  UIScreen.mainScreen().bounds.size.width * coverImageSize.height / coverImageSize.width
            profileCoverHeight = profileCoverOriginalScreenHeight + headerHeightOffset
        }
        
        postTableView.estimatedRowHeight = postTableView.rowHeight
        postTableView.rowHeight = UITableViewAutomaticDimension
        
  
           }
    
    
//    override func viewDidAppear(animated: Bool) {
//        if let sceneDetailViewController = storyboard?.instantiateViewControllerWithIdentifier("SceneDetailViewController"){
//            sceneDetailViewController.model
//            sceneDetailViewController.transitioningDelegate = self
//            
//            presentViewController(sceneDetailViewController, animated: true, completion: nil)
//        }
//        
//
//    }
//    
    //additional setup
    override func viewDidLayoutSubviews() {
        //change the constraint programmatically here
        updateAvator()
        profileCoverHeightConstraint.constant = profileCoverHeight
        setButton()
        setupThemeSlideCollectionView()
        
        
        tableHeaderView.frame.size.height = tableHeaderView.systemLayoutSizeFittingSize(UILayoutFittingCompressedSize).height
        //make sure the header view contans the exact necessary height given it's dynamic content
    }
    
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    //    override func preferredStatusBarStyle() -> UIStatusBarStyle {
    //        return .LightContent
    //    }
    
    
    override func prefersStatusBarHidden() -> Bool {
        return true
    }
    
    
    func strechProfileCover(){
        var coverHeaderRect = CGRect(x: 0, y: 0, width: profileCover.bounds.width, height: profileCoverHeight)
        var caculatedHeight = profileCoverHeight - postTableView.contentOffset.y
        var caculatedOrigin = postTableView.contentOffset.y
        
        if caculatedHeight < profileCoverOriginalScreenHeight {
            //minimize the picture until its originalScreenHeight
            caculatedHeight = profileCoverOriginalScreenHeight
            caculatedOrigin = headerHeightOffset
        }
        coverHeaderRect.size.height = caculatedHeight
        coverHeaderRect.origin.y  = caculatedOrigin
        profileCover.frame = coverHeaderRect
    }
    
    func updateAvator(){
        profileAvator.layer.cornerRadius = profileAvator.frame.size.width / 2;
        profileAvator.clipsToBounds = true
        profileAvator.layer.borderColor = UIColor.whiteColor().CGColor
        profileAvator.layer.borderWidth = 3
        //profileAvator.layer.cornerRadius = 6.0;
    }
    
    
    //additional set up for action button below the avator
    func setButton(){
        profileFollowButton?.becomeMagentaButton()
    }
    
    func setupThemeSlideCollectionView(){
        //the size for the theme image
        themeImageSize.width = (UIScreen.mainScreen().bounds.size.width - themeSlideConstant.sectionEdgeInset.left - 2*themeSlideConstant.lineSpace) / themeSlideConstant.maxVisibleThemeCount
        themeImageSize.height = themeImageSize.width / themeSlideConstant.themeImageAspectRatio
        
        //the height for the themeCollectionView
        themeSlideHeightConstraint.constant = themeImageSize.height + themeSlideConstant.sectionEdgeInset.top + themeSlideConstant.sectionEdgeInset.bottom + themeSlideConstant.precicitionOffset
    }
    
    
    
    

    
    
    
    
}

extension ProfileViewController: UIScrollViewDelegate{
    func scrollViewDidScroll(scrollView: UIScrollView) {
        strechProfileCover()
    }
}


// MARK:: horizontal theme slider, Extension for UICollectionViewDelegate, UICollectionViewDataSource and UICollectionViewDelegateFlowLayout protocol
extension ProfileViewController: UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout{
    func collectionView(collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return themeNames.count
    }
    
    func collectionView(collectionView: UICollectionView, cellForItemAtIndexPath indexPath: NSIndexPath) -> UICollectionViewCell {
        let themeCell = collectionView.dequeueReusableCellWithReuseIdentifier(themeSlideConstant.themeCellReuseIdentifier, forIndexPath: indexPath) as! ThemeCollectionViewCell
        themeCell.layer.cornerRadius = StyleSchemeConstant.horizontalSlider.horizontalSliderCornerRadius
        themeCell.themeImage.image = UIImage(named: themeImages[indexPath.row])
        themeCell.imageViewSize = themeImageSize
        themeCell.themeName.text = themeNames[indexPath.row]
        themeCell.layoutIfNeeded()
        return themeCell
    }
    
    
    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, insetForSectionAtIndex section: Int) -> UIEdgeInsets {
        return UIEdgeInsets(top: themeSlideConstant.sectionEdgeInset.top, left: themeSlideConstant.sectionEdgeInset.left, bottom: themeSlideConstant.sectionEdgeInset.bottom, right: themeSlideConstant.sectionEdgeInset.right)
    }
    
    
    // **if is set to horizontal scrolling, the line spacing is the space between each column**
    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, minimumLineSpacingForSectionAtIndex section: Int) -> CGFloat {
        return themeSlideConstant.lineSpace
    }
    
    
    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAtIndexPath indexPath: NSIndexPath) -> CGSize {
        return CGSize(width: themeImageSize.width, height: themeImageSize.height)
    }
}



// MARK:: Post Rows, Extension for UITableViewDelegate and UITableViewDataSource protocol
extension ProfileViewController: UITableViewDelegate, UITableViewDataSource{
    func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        return 1
    }
    
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1//posts.count
    }
    
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("postCell") as! PostTableViewCell
        return cell
    }
    
    func tableView(tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        let sectionHeaderView = UIView()

        //border view
        let borderView = UIView()
        borderView.backgroundColor = UIColor(red: 239 / 255.0, green: 239 / 255.0, blue: 244 / 255.0, alpha: 1)
        borderView.frame = CGRect(x: 0, y: 0, width: tableView.frame.size.width, height: 1)

        //date view
        let sectionLabel = UILabel()
        sectionLabel.text = "WEEK 4TH, JAN · 2016"
        sectionLabel.frame = CGRect(x: 18, y: 12, width: 180, height: 20)
        sectionLabel.font = UIFont.systemFontOfSize(13, weight: UIFontWeightMedium)
        sectionLabel.textColor = UIColor(red: 20 / 255.0, green:  20 / 255.0, blue:  20 / 255.0, alpha: 1)
        
        sectionHeaderView.addSubview(borderView)
        sectionHeaderView.addSubview(sectionLabel)
        
        return sectionHeaderView
    }
}

extension ProfileViewController: UIViewControllerTransitioningDelegate{
    func animationControllerForPresentedController(presented: UIViewController, presentingController presenting: UIViewController, sourceController source: UIViewController) -> UIViewControllerAnimatedTransitioning? {
        print("hello, i'm ")
        return closeUpTransition
    }
    
}




