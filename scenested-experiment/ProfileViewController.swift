//
//  ProfileViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 4/10/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit
import Photos

class ProfileViewController: StrechableHeaderViewController {

    @IBOutlet weak var profileCover: UIImageView!
    
    @IBOutlet weak var profileCoverHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var themeSlideHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var profileAvator: UIImageView!
    
    @IBOutlet weak var profileButtonBelowCover: UIButton!
    
    
    @IBOutlet weak var themesCollectionView: UICollectionView!
    
    
    @IBOutlet weak var globalView: UITableView!
    
    @IBOutlet weak var tableHeaderView: UIView!
    
    
    @IBOutlet weak var profileButtonBelowCoverWidthConstaint: NSLayoutConstraint!

    
    @IBAction func closeEditProfile(unwindSegue: UIStoryboardSegue){
    }
    
    
    @IBAction func closeAddTheme(unwindSegue: UIStoryboardSegue){
    }
    
    
     // MARK:: Avator tap gesture configuration
    @IBAction func tapAvator(sender: UITapGestureRecognizer) {
        let alert = UIAlertController(title: "Change Profile Picture", message: nil, preferredStyle: .ActionSheet)
        let chooseExistingAction = UIAlertAction(title: "Choose from Library", style: .Default, handler: { (action) -> Void in
            self.chooseFromLibarary()
        })
        let takePhotoAction = UIAlertAction(title: "Take Photo", style: .Default, handler: nil)

        let cancelAction = UIAlertAction(title: "Cancel", style: .Cancel, handler: nil)

        alert.addAction(takePhotoAction)
        alert.addAction(chooseExistingAction)
        alert.addAction(cancelAction)
        self.presentViewController(alert, animated: true, completion: nil)
    }
    
    
    private var profileCoverHeight: CGFloat = 0
    private var headerHeightOffset: CGFloat = 0 // make the cover's height little bit larger than the original screen height
    private var profileCoverOriginalScreenHeight: CGFloat = 0
    
    private var themeImageSize: CGSize = CGSizeZero //the size of the individual theme UIImageView
    
    private let closeUpTransition = CloseUpAnimator()
    
    
    private var selectedThumbnailItemInfo = CloseUpEffectSelectedItemInfo() //the thumbnail frame(such as sceneThumbnail or themeThumbnail) on which was tapped
    
//    private var interactingCollectionView: UICollectionView?
    
    private var selectedThumbnailScene: Scene?
    
    private let sectionHeaderHeight:CGFloat = 46
    
    private let initialContentOffsetTop: CGFloat = 64.0

    
    private let isUserOwnProfile = true
    
//    private let myImagePicker = UIImagePickerController()
    
    
    
    
    /* define the style constant for the theme slide  */
    private struct themeSlideConstant{
        struct sectionEdgeInset{
            static let top:CGFloat = 0
            static let left:CGFloat = 12
            static let bottom:CGFloat = 14
            static let right:CGFloat = 14
        }
        
        //the space between each item
        static let lineSpace: CGFloat = 6
        static let maxVisibleThemeCount: CGFloat = 2.6
        //the max number of theme that is allowed to display at the screen
        static let themeImageAspectRatio:CGFloat = 5 / 6
        static let precicitionOffset: CGFloat = 1 //prevent the height of the collectionView from less than the total of the cell height and inset during the calculation
        static let themeCellReuseIdentifier: String = "themeCell"
    }
    
    
    //themes data source
    //let themeNames: [String] = ["This is my first coustic fingerstyle guitar concert in New York", "Glad to see this year US Open Final", "My first hackathon ever!"]
    let themeNames: [String] =  ["GUITAR", "TENNIS", "PROGRAMMING"]
    let themeImages: [String] = ["theme1", "theme2", "thumb_2"]
    
    static let scene1 = Scene(id: 1, imageUrl: "cover3", themeName: "TENNIS", postText: "Great to be able to experience this year's #USOpen", postDate: "Sep 4, 2015")
    static let scene2 = Scene(id: 3, imageUrl: "100_1288", themeName: "PROGRAMMING", postText: "This is my first hackathon at Lehman Collge", postDate: "May 02, 2015")

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
    


    
//    var profileScenes:[WeekScenes] = [
//                    weekScene1,
//                    weekScene2,
//                    weekScene3,
//                    weekScene4
//                ]
    
    var profileScenes:[WeekScenes] = []
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        
//        myImagePicker.delegate = self
        
        themesCollectionView.delegate = self
        themesCollectionView.dataSource = self
//        globalView.delegate = self
//        globalView.dataSource = self
//        self.navigationController?.navigationBarHidden = true
        
        
        //self.tabBarController?.tabBar.hidden = true
//        globalView.alwaysBounceVertical = true
        
        profileCover.image = UIImage(named: "cover3")
        if let coverImageSize = profileCover.image?.size{
            profileCoverOriginalScreenHeight =  UIScreen.mainScreen().bounds.size.width * coverImageSize.height / coverImageSize.width
            profileCoverHeight = profileCoverOriginalScreenHeight + headerHeightOffset
            profileCoverHeightConstraint.constant = profileCoverHeight
        }
        
        globalView.estimatedRowHeight = globalView.rowHeight
        globalView.rowHeight = UITableViewAutomaticDimension
        
        
        if isUserOwnProfile{
           addPostSceneBtn()
        }else{
            self.navigationItem.rightBarButtonItem = nil
        }
        
        
        
//        let cameraPostBtn = UIButton()
//        cameraPostBtn.setImage(UIImage(named: "camera-icon-1"), forState: .Normal)
//        cameraPostBtn.frame = CGRectMake(0, 0, 20, 20)
//        //btnName.addTarget(self, action: Selector("action"), forControlEvents: .TouchUpInside)
//        
//        //.... Set Right/Left Bar Button item
//        let rightBarButton = UIBarButtonItem(customView: cameraPostBtn)
//        self.navigationItem.rightBarButtonItem = rightBarButton
    }
    
    

    //additional setup
    override func viewDidLayoutSubviews() {
        //change the constraint programmatically here
        updateAvator()
        setButton()
        setupThemeSlideCollectionView()
       // tableHeaderView.frame.size.height = tableHeaderView.systemLayoutSizeFittingSize(UILayoutFittingCompressedSize).height
        //make sure the header view contans the exact necessary height given it's dynamic content
    }
    
    override func viewDidAppear(animated: Bool) {
        //stretchy header set up
        self.globalScrollView = globalView
        self.coverImageView = profileCover
        self.coverHeight = profileCover.bounds.size.height
        self.defaultInitialContentOffsetTop = initialContentOffsetTop
        self.stretchWhenContentOffsetLessThanZero = true
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
//    override func prefersStatusBarHidden() -> Bool {
//        return true
//    }
    
    
    func updateAvator(){
        profileAvator.becomeCircleAvator()
    }
    
    
    //additional set up for action button below the avator
    func setButton(){
        if isUserOwnProfile{
            profileButtonBelowCover?.becomeEditProfileButton()
        }else{
            profileButtonBelowCover?.becomeFollowButton()

        }
    }
    
    func setupThemeSlideCollectionView(){
        //the size for the theme image
        themeImageSize.width = (UIScreen.mainScreen().bounds.size.width - themeSlideConstant.sectionEdgeInset.left - 2*themeSlideConstant.lineSpace) / themeSlideConstant.maxVisibleThemeCount
        themeImageSize.height = themeImageSize.width / themeSlideConstant.themeImageAspectRatio
        //the height for the themeCollectionView
        themeSlideHeightConstraint.constant = themeImageSize.height + themeSlideConstant.sectionEdgeInset.top + themeSlideConstant.sectionEdgeInset.bottom + themeSlideConstant.precicitionOffset
        
    }
    
    func addPostSceneBtn(){
        let barBtnItem = UIBarButtonItem()
        barBtnItem.title =  "＋Post"
        barBtnItem.setTitleTextAttributes([NSFontAttributeName: UIFont.systemFontOfSize(17, weight: UIFontWeightMedium), NSForegroundColorAttributeName: StyleSchemeConstant.themeColor ] , forState: .Normal)
        
        self.navigationItem.rightBarButtonItem = barBtnItem
    }
    
    
    func chooseFromLibarary(){
        if isAvailabeToPickFromLibaray(){
            let savedAlbumSource = UIImagePickerControllerSourceType.SavedPhotosAlbum
            if !UIImagePickerController.isSourceTypeAvailable(savedAlbumSource){
                //the given source type is not availabe
                return
            }
            //once given source type is available, check which media type is available
            let imagePicker = UIImagePickerController()
            imagePicker.delegate = self
            if let mediaTypes = UIImagePickerController.availableMediaTypesForSourceType(savedAlbumSource){
                imagePicker.mediaTypes = mediaTypes
                self.presentViewController(imagePicker, animated: true, completion: nil)
            }
            
            
        }else{
            print("not available")
        }
    }
    
    
    
    
    
    
    //check whether the App is allowed to get access to the user's photo libarary, ask for authorization, otherwise
    func isAvailabeToPickFromLibaray() -> Bool{
        let status = PHPhotoLibrary.authorizationStatus()
        switch status {
        case .Authorized:
            return true
        case .NotDetermined:
            PHPhotoLibrary.requestAuthorization(){PHAuthorizationStatus -> Void  in}
            return false
        case .Restricted:
            return false
        case .Denied:
            let alert = UIAlertController(title: "Authorization Needed", message: "Authorization needed in order to choose photo from libarary", preferredStyle: .Alert)
            let dontAllowAction = UIAlertAction(title: "Don't Allow", style: .Default, handler: nil)
            let goToSettingAction = UIAlertAction(title: "Ok", style: .Default, handler: {
                _ in
                if let url = NSURL(string: UIApplicationOpenSettingsURLString){
                     UIApplication.sharedApplication().openURL(url)
                }
            })
            alert.addAction(dontAllowAction)
            alert.addAction(goToSettingAction)
            self.presentViewController(alert, animated: true, completion: nil)
            return false
        }
    }
    
   
    

    
    override func scrollViewDidScroll(scrollView: UIScrollView) {
        super.scrollViewDidScroll(scrollView)
        
        //reset all other visible rows section header view to white color
//        if let visiableIndexPathForCell = globalView.indexPathsForVisibleRows{
//            for indexPath in visiableIndexPathForCell{
//                if let otherHeaderView = globalView.headerViewForSection(indexPath.section){
//                    otherHeaderView.contentView.backgroundColor = UIColor.whiteColor()
//                    otherHeaderView.layer.borderColor = .None
//                    otherHeaderView.layer.borderWidth = 0
//                    otherHeaderView.alpha = 1.0
//                }
//            }
//            
//            if let firstVisiableIndexPathForCell = visiableIndexPathForCell.first{
//                if let firstVisibleCell = globalView.cellForRowAtIndexPath(firstVisiableIndexPathForCell){
//                    if firstVisibleCell.frame.origin.y < sectionHeaderHeight + globalView.contentOffset.y{
//                        if let headerView = globalView.headerViewForSection(firstVisiableIndexPathForCell.section){
//                            
//                            headerView.contentView.backgroundColor = UIColor(red: 246/255.0, green: 246/255.0, blue: 246/255.0, alpha: 1)
//                            //                            headerView.alpha = 0.97
//                            headerView.layer.borderColor = UIColor(red: 240/255.0, green: 240/255.0, blue: 240/255.0, alpha: 1).CGColor
//                            headerView.layer.borderWidth = 0.8
//                        }
//                    }
//                }
//            }
//        }
        

    }
    
    
    
}

//extension ProfileViewController: UIScrollViewDelegate{
//    func scrollViewDidScroll(scrollView: UIScrollView) {
//        strechProfileCover()
//        
//        
//        //reset all other visible rows section header view to white color
//        if let visiableIndexPathForCell = globalView.indexPathsForVisibleRows{
//            for indexPath in visiableIndexPathForCell{
//                if let otherHeaderView = globalView.headerViewForSection(indexPath.section){
//                    otherHeaderView.contentView.backgroundColor = UIColor.whiteColor()
//                    otherHeaderView.layer.borderColor = .None
//                    otherHeaderView.layer.borderWidth = 0
//                    otherHeaderView.alpha = 1.0
//                }
//            }
//            
//            if let firstVisiableIndexPathForCell = visiableIndexPathForCell.first{
//                if let firstVisibleCell = globalView.cellForRowAtIndexPath(firstVisiableIndexPathForCell){
//                    if firstVisibleCell.frame.origin.y < sectionHeaderHeight + globalView.contentOffset.y{
//                        if let headerView = globalView.headerViewForSection(firstVisiableIndexPathForCell.section){
//                            
//                            headerView.contentView.backgroundColor = UIColor(red: 246/255.0, green: 246/255.0, blue: 246/255.0, alpha: 1)
////                            headerView.alpha = 0.97
//                            headerView.layer.borderColor = UIColor(red: 240/255.0, green: 240/255.0, blue: 240/255.0, alpha: 1).CGColor
//                            headerView.layer.borderWidth = 0.8
//                        }
//                    }
//                }
//            }
//        }
//    }
//}



// MARK:: built in protocol

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

//MARK:: UIImagePickerControllerDelegate and UINavigationControllerDelegate protocol

extension ProfileViewController: UIImagePickerControllerDelegate{
    func imagePickerController(picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : AnyObject]) {

        print(info[UIImagePickerControllerOriginalImage])
    }
    
    func imagePickerControllerDidCancel(picker: UIImagePickerController) {
       self.dismissViewControllerAnimated(true, completion: nil)
    }
}

extension ProfileViewController: UINavigationControllerDelegate{
    
}




// MARK:: custom protocol
extension ProfileViewController: PostCollectionViewProtocol{
    func didTapCell(collectionView: UICollectionView, indexPath: NSIndexPath, scene: Scene, selectedItemInfo: CloseUpEffectSelectedItemInfo) {
//        self.interactingCollectionView = collectionView
        //present the sceneDetailViewController
     let sceneDetailViewController = storyboard?.instantiateViewControllerWithIdentifier("sceneDetailViewControllerIden") as! SceneDetailViewController
//          let sceneDetailViewController = sceneDetailNavigationController.viewControllers[0] as! SceneDetailViewController
//        
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


