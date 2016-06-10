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
    
    
    @IBOutlet weak var globalView: UITableView!
    
    @IBOutlet weak var tableHeaderView: UIView!

    
    
    private var profileCoverHeight: CGFloat = 0
    private var headerHeightOffset: CGFloat = 30 // make the cover's height little bit larger than the original screen height
    private var profileCoverOriginalScreenHeight: CGFloat = 0
    
    private var themeImageSize: CGSize = CGSizeZero //the size of the individual theme UIImageView
    
    private let closeUpTransition = CloseUpAnimator()
    
    
    private var selectedThumbnailItemInfo = CloseUpEffectSelectedItemInfo() //the thumbnail frame(such as sceneThumbnail or themeThumbnail) on which was tapped
    
//    private var interactingCollectionView: UICollectionView?
    
    private var selectedThumbnailScene: Scene?
    
    private let sectionHeaderHeight:CGFloat = 46
    
    
    
    /* define the style constant for the theme slide  */
    private struct themeSlideConstant{
        struct sectionEdgeInset{
            static let top:CGFloat = 0
            static let left:CGFloat = 14
            static let bottom:CGFloat = 0
            static let right:CGFloat = 14
        }
        
        //the space between each item
        static let lineSpace: CGFloat = 7
        static let maxVisibleThemeCount: CGFloat = 2.2 //the max number of theme that is allowed to display at the screen
        static let themeImageAspectRatio:CGFloat = 4/5
        static let precicitionOffset: CGFloat = 1 //prevent the height of the collectionView from less than the total of the cell height and inset during the calculation
        static let themeCellReuseIdentifier: String = "themeCell"
    }
    
    
    //themes data source
    //let themeNames: [String] = ["This is my first coustic fingerstyle guitar concert in New York", "Glad to see this year US Open Final", "My first hackathon ever!"]
    let themeNames: [String] = ["GUITAR", "TENNIS", "PROGRAMMING"]
    let themeImages: [String] = ["theme1", "theme2", "thumb_2"]
    
    static let scene1 = Scene(id: 1, imageUrl: "cover3", themeName: "TENNIS", postText: "Great to be able to experience this year's #USOpen", postDate: "Sep 4, 2015")
    static let scene2 = Scene(id: 3, imageUrl: "thumb_2", themeName: "PROGRAMMING", postText: "This is my first hackathon at Lehman Collge", postDate: "May 02, 2015")

    static let scene3 = Scene(id: 2, imageUrl: "thumb_1", themeName: "GUITAR", postText: "This is my first time to see a live acoustic guitar concert since I picked up guitar about five years ago. #TraceBundy", postDate: "May 17, 2014")
    static let scene4 = Scene(id: 4, imageUrl: "canada", themeName: "TRAVEL", postText: "A nice trip with my family to Canada, see the great Fall", postDate: "May 17, 2014")

    static let scene5 = Scene(id: 5, imageUrl: "libarary", themeName: "PROGRAMMING", postText: "A beautiful sunset near the company where I was interned in during my freshman summer", postDate: "May 17, 2014")
    
    static let scene6 = Scene(id: 6, imageUrl: "cover", themeName: "TENNIS", postText: "A friend of mine showed the a tennis park near the huston river, truly stunning", postDate: "May 17, 2014")
    
    static let scene7 = Scene(id: 7, imageUrl: "garden", themeName: "TENNIS", postText: "Roger and Dimitrov played an exihibition match in Madision Sqaure Garden",postDate: "May 17, 2014")

 

    //post data source
    //each element in posts is posts from the same week, for example, post1 and post2 are from week 1, Jan 2015, post3 is from week 3, Jan, 2016
    
    static let  weekScene1: WeekScenes = WeekScenes(scenes: [scene1, scene2], weekDisplayInfo: "WEEK 4TH, JAN · 2016")
    static let weekScene2: WeekScenes = WeekScenes(scenes: [scene3], weekDisplayInfo: "WEEK 2ND, JAN · 2015")
    static let weekScene3: WeekScenes = WeekScenes(scenes: [scene5, scene6, scene7], weekDisplayInfo: "WEEK 3RD, MAR · 2014")
    static let weekScene4: WeekScenes = WeekScenes(scenes: [scene4], weekDisplayInfo: "WEEK 1ST, SEP · 2013")
    


    
    var profileScenes:[WeekScenes] = [
                    weekScene1,
                    weekScene2,
                    weekScene3,
                    weekScene4
                ]
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        themesCollectionView.delegate = self
        themesCollectionView.dataSource = self
        globalView.delegate = self
        globalView.dataSource = self
//        self.tabBarController?.tabBar.hidden = true
        self.navigationController?.navigationBarHidden = true
        
        globalView.alwaysBounceVertical = true
        //        self.automaticallyAdjustsScrollViewInsets = false
        
        profileCover.image = UIImage(named: "cover3")
        if let coverImageSize = profileCover.image?.size{
            profileCoverOriginalScreenHeight =  UIScreen.mainScreen().bounds.size.width * coverImageSize.height / coverImageSize.width
            profileCoverHeight = profileCoverOriginalScreenHeight + headerHeightOffset
        }
        
        globalView.estimatedRowHeight = globalView.rowHeight
        globalView.rowHeight = UITableViewAutomaticDimension
    }

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
    
    override func prefersStatusBarHidden() -> Bool {
        return true
    }
    
    
    
//    override func preferredStatusBarStyle() -> UIStatusBarStyle {
//        return .LightContent
//    }
//    
    
    
    
    func strechProfileCover(){
        var coverHeaderRect = CGRect(x: 0, y: 0, width: profileCover.bounds.width, height: profileCoverHeight)
        var caculatedHeight = profileCoverHeight - globalView.contentOffset.y
        var caculatedOrigin = globalView.contentOffset.y
        
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
    
    //set data for the SceneDetailViewController
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        if  segue.identifier == "showSceneDetail"{
            if let detaileViewController = segue.destinationViewController as? SceneDetailViewController{
//               print( self.selectedThumbnailScene)
            
            }
        }
    }
    
}

extension ProfileViewController: UIScrollViewDelegate{
    func scrollViewDidScroll(scrollView: UIScrollView) {
        strechProfileCover()
        
        
        //reset all other visible rows section header view to white color
        if let visiableIndexPathForCell = globalView.indexPathsForVisibleRows{
            for indexPath in visiableIndexPathForCell{
                if let otherHeaderView = globalView.headerViewForSection(indexPath.section){
                    otherHeaderView.contentView.backgroundColor = UIColor.whiteColor()
                    otherHeaderView.layer.borderColor = .None
                    otherHeaderView.layer.borderWidth = 0
                    otherHeaderView.alpha = 1.0
                }
            }
            
            if let firstVisiableIndexPathForCell = visiableIndexPathForCell.first{
                if let firstVisibleCell = globalView.cellForRowAtIndexPath(firstVisiableIndexPathForCell){
                    if firstVisibleCell.frame.origin.y < sectionHeaderHeight + globalView.contentOffset.y{
                        if let headerView = globalView.headerViewForSection(firstVisiableIndexPathForCell.section){
                            
                            headerView.contentView.backgroundColor = UIColor(red: 246/255.0, green: 246/255.0, blue: 246/255.0, alpha: 1)
//                            headerView.alpha = 0.97
                            headerView.layer.borderColor = UIColor(red: 240/255.0, green: 240/255.0, blue: 240/255.0, alpha: 1).CGColor
                            headerView.layer.borderWidth = 0.8
                        }
                    }
                }
            }
        }
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
    //defines how many weeks the profile user has
    func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        return profileScenes.count
    }
    
    //each section is a collection of the same week
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1
    }
    
    //define the data source for a specific week
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell{
        let cell = tableView.dequeueReusableCellWithIdentifier("postCell") as! PostTableViewCell
        cell.postCollectionViewDelegate = self
        cell.weekScenes = profileScenes[indexPath.section]
//        cell.parentTableView = globalView
        
        return cell
    }
    
    func tableView(tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        let sectionHeaderView = UITableViewHeaderFooterView()
        //border view
        let borderView = UIView()
        borderView.backgroundColor = UIColor(red: 239 / 255.0, green: 239 / 255.0, blue: 244 / 255.0, alpha: 1)
        borderView.frame = CGRect(x: 0, y: 0, width: tableView.frame.size.width, height: 1)
       
        //date view
        let sectionLabel = UILabel()
        sectionLabel.text = profileScenes[section].weekDisplayInfo
        sectionLabel.frame = CGRect(x: 16, y: 14, width: 180, height: 18)
        sectionLabel.font = UIFont.systemFontOfSize(13, weight: UIFontWeightMedium)
        sectionLabel.textColor = UIColor(red: 20 / 255.0, green:  20 / 255.0, blue:  20 / 255.0, alpha: 1)
        
        sectionHeaderView.addSubview(borderView)
        sectionHeaderView.addSubview(sectionLabel)
        sectionHeaderView.contentView.backgroundColor = UIColor.whiteColor()

        return sectionHeaderView
    }
    
    func tableView(tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
        return sectionHeaderHeight
    }
    
//     func tableView(tableView: UITableView, willDisplayHeaderView view: UIView, forSection section: Int) {
//        let header: UITableViewHeaderFooterView = view as! UITableViewHeaderFooterView //recast your view as a UITableViewHeaderFooterView
//        header.contentView.backgroundColor = UIColor(red: 0/255, green: 181/255, blue: 229/255, alpha: 1.0) //make the background color light blue
//        header.textLabel!.textColor = UIColor.whiteColor() //make the text white
//        header.alpha = 0.5 //make the header transparent
//    }
//    
    

    
   
    

}

extension ProfileViewController: UIViewControllerTransitioningDelegate{
    func animationControllerForPresentedController(presented: UIViewController, presentingController presenting: UIViewController, sourceController source: UIViewController) -> UIViewControllerAnimatedTransitioning? {
        closeUpTransition.selectedItemInfo = selectedThumbnailItemInfo
        return closeUpTransition
    }
    
    func animationControllerForDismissedController(dismissed: UIViewController) -> UIViewControllerAnimatedTransitioning? {
        closeUpTransition.presenting = true
        return closeUpTransition
    }
    
}

extension ProfileViewController: PostCollectionViewProtocol{
    func didTapCell(collectionView: UICollectionView, indexPath: NSIndexPath, scene: Scene, selectedItemInfo: CloseUpEffectSelectedItemInfo) {
//        self.interactingCollectionView = collectionView
        //present the sceneDetailViewController
     let sceneDetailViewController = storyboard?.instantiateViewControllerWithIdentifier("sceneDetailViewControllerIden") as! SceneDetailViewController
        sceneDetailViewController.scene = scene
        sceneDetailViewController.transitioningDelegate = self
        self.selectedThumbnailScene = scene
        self.selectedThumbnailItemInfo = selectedItemInfo
        self.presentViewController(sceneDetailViewController, animated: true, completion: nil)
    }
}





extension ProfileViewController: CloseUpMainProtocol{
    func closeUpTransitionGlobalScrollView() -> UIScrollView {
        return globalView
    }
}


