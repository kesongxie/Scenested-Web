//
//  ProfileViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 4/10/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class ProfileViewController: EditableProfileViewController {

    @IBOutlet weak var profileCover: UIImageView!
    
    @IBOutlet weak var profileCoverHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var themeSlideHeightConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var profileAvator: UIImageView!
    
    @IBOutlet weak var profileButtonBelowCover: UIButton!
    
    
    @IBOutlet weak var themesCollectionView: UICollectionView!
    
    
    @IBOutlet weak var globalView: UITableView!
    
    @IBOutlet weak var tableHeaderView: UIView!
    
    
    @IBOutlet weak var profileButtonBelowCoverWidthConstaint: NSLayoutConstraint!

    @IBOutlet weak var addThemePlusIcon: UIButton!
    @IBAction func closeEditProfile(unwindSegue: UIStoryboardSegue){
    }
    
    
    @IBAction func cancelAddTheme(unwindSegue: UIStoryboardSegue){
    }
    
    @IBAction func saveAddTheme(unwindSegue: UIStoryboardSegue){
        if let addThemeVC = unwindSegue.sourceViewController as? AddThemeViewController{
            let themeCover = addThemeVC.themeCoverImage
            let themeName = addThemeVC.themeNameTextField.text
            let newTheme = Theme(id: 0, imageUrl: "cover4", themeName: themeName!, createdDate: "")
            profileThemes.insert(newTheme, atIndex: 0)
        }
    }
    
    
    @IBAction func profileBelowBtnTriggered(sender: UIButton) {
        if isUserOwnProfile{
            //present edit profile
            if let editProfileNavi = storyboard?.instantiateViewControllerWithIdentifier("EditProfileNaviIden"){
                self.presentViewController(editProfileNavi, animated: true, completion: nil)
            }
        }else{
            //add follow
            
        }
    }
    
    
    @IBAction func addThemeBoxTapped(sender: UITapGestureRecognizer) {
        let alert = UIAlertController(title: "Add Cover for New Theme", message: nil, preferredStyle: .ActionSheet)
        
        let chooseExistingAction = UIAlertAction(title: "Choose from Library", style: .Default, handler: { (action) -> Void in
            self.chooseFromLibarary()
            self.hideAddThemeBox()
        })
        let takePhotoAction = UIAlertAction(title: "Take Photo", style: .Default, handler: {
            (action) -> Void in
            self.takePhoto()
            self.hideAddThemeBox()
        })
        let cancelAction = UIAlertAction(title: "Cancel", style: .Cancel, handler: nil)
        
        alert.addAction(takePhotoAction)
        alert.addAction(chooseExistingAction)
        alert.addAction(cancelAction)
        imagePickerUploadPhotoFor = UploadPhotoFor.themeCover
        self.presentViewController(alert, animated: true, completion: nil)
    }
    
    
    private var profileCoverHeight: CGFloat = 0
    private var headerHeightOffset: CGFloat = 0 // make the cover's height little bit larger than the original screen height
    private var profileCoverOriginalScreenHeight: CGFloat = 0
    
    private var themeImageSize: CGSize = CGSizeZero //the size of the individual theme UIImageView
    
    private let closeUpTransition = CloseUpAnimator()
    
    
    private var selectedThumbnailItemInfo = CloseUpEffectSelectedItemInfo() //the thumbnail frame(such as sceneThumbnail or themeThumbnail) on which was tapped
    
    
    private var selectedThumbnailScene: Scene?
    
    private let sectionHeaderHeight:CGFloat = 46
    
    private let initialContentOffsetTop: CGFloat = 64.0

    
    private let isUserOwnProfile = true
    
    
    
    
    
    /* define the style constant for the theme slide  */
    private struct themeSlideConstant{
        struct sectionEdgeInset{
            static let top:CGFloat = 0
            static let left:CGFloat = 12
            static let bottom:CGFloat = 0
            static let right:CGFloat = 14
        }
        
        static let contentInsetWithAddThemeBox: UIEdgeInsets = UIEdgeInsets(top: 0, left: 14, bottom: 0, right: 14)
        static let contentInset: UIEdgeInsets = UIEdgeInsets(top: 0, left: 0, bottom: 0, right: 0)

        
        //the space between each item
        static let lineSpace: CGFloat = 8
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
    
    let themes = [
        Theme(id: 3, imageUrl: "thumb_2", themeName: "PROGRAMMING", createdDate: ""),
        Theme(id: 2, imageUrl: "theme2", themeName: "TENNIS", createdDate: ""),
        Theme(id: 1, imageUrl: "theme1", themeName: "GUITAR", createdDate: "")
    ]

    static let loggedInUser = User(id: 1, username: "kesongxie", fullname: "Kesong Xie", avatorUrl: "avator2", coverUrl: "")
    
    static let scene1 = Scene(id: 1, postUser: loggedInUser,imageUrl: "cover3", themeName: "TENNIS", postText: "Great to be able 😂 😙 #爱 to #experience this year's #USOpen@great great", postTime: "3h")
    static let scene2 = Scene(id: 3, postUser: loggedInUser, imageUrl: "100_1288", themeName: "PROGRAMMING", postText: "This is my first hackathon at Lehman Collge", postTime: "1d")

    static let scene3 = Scene(id: 2, postUser: loggedInUser, imageUrl: "thumb_1", themeName: "GUITAR", postText: "This is my first time to see a live acoustic guitar concert since I picked up guitar about five years ago. #TraceBundy", postTime: "3w")
    static let scene4 = Scene(id: 4, postUser: loggedInUser, imageUrl: "canada", themeName: "TRAVEL", postText: "A nice trip with my family to Canada, see the great Fall", postTime: "May 17, 2014")

    static let scene5 = Scene(id: 5, postUser: loggedInUser, imageUrl: "libarary", themeName: "PROGRAMMING", postText: "A beautiful sunset near the company where I was interned in during my freshman summer", postTime: "May 17, 2014")
    
    static let scene6 = Scene(id: 6, postUser: loggedInUser, imageUrl: "cover", themeName: "TENNIS", postText: "A friend of mine showed the a tennis park near the huston river, truly stunning", postTime: "May 17, 2014")
    
    static let scene7 = Scene(id: 7, postUser: loggedInUser, imageUrl: "garden", themeName: "TENNIS", postText: "Roger and Dimitrov played an exihibition match in Madision Sqaure Garden",postTime: "May 17, 2014")

     //post data source
    //each element in posts is posts from the same week, for example, post1 and post2 are from week 1, Jan 2015, post3 is from week 3, Jan, 2016
    
    static let  weekScene1: WeekScenes = WeekScenes(scenes: [scene1, scene2], weekDisplayInfo: "WEEK 4TH, JAN · 2016")
    static let weekScene2: WeekScenes = WeekScenes(scenes: [scene3], weekDisplayInfo: "WEEK 2ND, JAN · 2015")
    static let weekScene3: WeekScenes = WeekScenes(scenes: [scene5, scene6, scene7], weekDisplayInfo: "WEEK 3RD, MAR · 2014")
    static let weekScene4: WeekScenes = WeekScenes(scenes: [scene4], weekDisplayInfo: "WEEK 1ST, SEP · 2013")
    

    var profileScenes:[Scene] = [scene1, scene2, scene3]
    
    
    
//    var profileScenes:[WeekScenes] = [
//                    weekScene1,
//                    weekScene2,
//                    weekScene3,
//                    weekScene4
//                ]
    
//    var profileScenes:[WeekScenes] = []
    
    var profileThemes:[Theme] = []
    {
        didSet{
            themesCollectionView.reloadData()
        }
    }
    
    
    private var addThemeBoxOpen:Bool = false
    private var themeSliderFrameSet:Bool = false
    
    private var isThemeActionSheetActive = false
    
    private var minTableHeaderHeight:CGFloat = 0
    
       
    override func viewDidLoad() {
        super.viewDidLoad()
        themesCollectionView.delegate = self
        themesCollectionView.dataSource = self
        globalView.delegate = self
        globalView.dataSource = self
        
        profileCover.image = UIImage(named: "cover3")
        if let coverImageSize = profileCover.image?.size{
            profileCoverOriginalScreenHeight =  UIScreen.mainScreen().bounds.size.width * coverImageSize.height / coverImageSize.width
            profileCoverHeight = profileCoverOriginalScreenHeight + headerHeightOffset
            profileCoverHeightConstraint.constant = profileCoverHeight
        }
        
        globalView.estimatedRowHeight = globalView.rowHeight
        globalView.rowHeight = UITableViewAutomaticDimension
        
        setUpTheme()
      
        
        if isUserOwnProfile{
            addPostSceneBtn()
            addTapGestureForAvator()
            addTapGestureForCover()
        }else{
            addThemePlusIcon.hidden = true
            self.navigationItem.rightBarButtonItem = nil
        }
        
        
       
        
    }
    
    func setUpTheme(){
        for theme in themes{
            profileThemes.insert(theme, atIndex: 0)
        }
    }
    
    
    
    func addTapGestureForAvator(){
        let tap = UITapGestureRecognizer(target: self, action: #selector(ProfileViewController.tapAvator))
        profileAvator.addGestureRecognizer(tap)
    }
    
    func addTapGestureForCover(){
        let tap = UITapGestureRecognizer(target: self, action: #selector(ProfileViewController.tapCover))
        profileCover.addGestureRecognizer(tap)
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
        
        if isUserOwnProfile && !themeSliderFrameSet{
            themesCollectionView.contentInset.left = -themeImageSize.width
            themeSliderFrameSet = true
        }
    
        
         minTableHeaderHeight = (tableHeaderView.systemLayoutSizeFittingSize(UILayoutFittingCompressedSize)).height
        tableHeaderView.frame.size.height = minTableHeaderHeight//the minimum height for the tableheader view

    }
    
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func updateAvator(){
        profileAvator.becomeCircleAvator()
    }
    
    
    //additional set up for ction button below the avator
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
        
        
        
        if isUserOwnProfile{
            themesCollectionView.contentInset = themeSlideConstant.contentInsetWithAddThemeBox
        }else{
            themesCollectionView.contentInset = themeSlideConstant.contentInset
        }
    }
    
    func addPostSceneBtn(){
        let barBtnItem = UIBarButtonItem()
        barBtnItem.title =  "＋Post"
        barBtnItem.setTitleTextAttributes([NSFontAttributeName: UIFont.systemFontOfSize(17, weight: UIFontWeightMedium), NSForegroundColorAttributeName: StyleSchemeConstant.themeColor ] , forState: .Normal)
        self.navigationItem.rightBarButtonItem = barBtnItem
    }
    
    func hideAddThemeBox(){
        addThemeBoxOpen = false
        UIView.animateWithDuration(0.2, animations: {
            self.themesCollectionView.contentInset.left = -self.themeImageSize.width
        })
    }
    
    func openAddThemeBox(){
        addThemeBoxOpen = true
        UIView.animateWithDuration(0.2, animations: {
            self.themesCollectionView.contentInset.left = themeSlideConstant.contentInsetWithAddThemeBox.left
        })
    }
    
    
    func themeLongPressed(gesture: UIGestureRecognizer){
        if !isThemeActionSheetActive{
            let actionSheet = UIAlertController(title: "\n\n\n\n\n", message: "", preferredStyle: .ActionSheet)
            
            
            let themeImageView = (gesture.view as! ThemeImageView)
            let thumbImage = themeImageView.image
            
            let margin: CGFloat = 10.0
            let thumbImageViewWidth: CGFloat = 100.0
            
            //construct the thumbnail on the action sheet title
            let thumbImageView = UIImageView(frame: CGRectMake(margin, margin, thumbImageViewWidth, thumbImageViewWidth))
            thumbImageView.image = thumbImage
            thumbImageView.clipsToBounds = true
            thumbImageView.contentMode = .ScaleAspectFill
            thumbImageView.layer.cornerRadius = 8.0
            actionSheet.view.addSubview(thumbImageView)
            
            //construct the label on the action sheet title
            let nameLabel = UILabel(frame: CGRectMake(thumbImageViewWidth + 2 * margin, margin, actionSheet.view.bounds.size.width - 5 * margin - thumbImageViewWidth, 30.0))
            nameLabel.text = themeImageView.themeName
            nameLabel.textColor = StyleSchemeConstant.themeMainTextColor
            nameLabel.font = UIFont.systemFontOfSize(15, weight: UIFontWeightSemibold)
            actionSheet.view.addSubview(nameLabel)

            
            
           
            let addSceneAction = UIAlertAction(title: "Add Scene", style: .Default, handler: nil)
            let deleteThemeAction = UIAlertAction(title: "Delete Theme", style: .Default, handler: nil)
            let doneAction = UIAlertAction(title: "Done", style: .Cancel, handler: {
             _ -> Void in
              self.isThemeActionSheetActive = false
            })
            actionSheet.addAction(addSceneAction)
            actionSheet.addAction(deleteThemeAction)
            actionSheet.addAction(doneAction)
            self.presentViewController(actionSheet, animated: true, completion: nil)
            isThemeActionSheetActive = true
        }
        
    }
    
    
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        if let themeVC = segue.destinationViewController as? ThemeViewController{
            
            if let selectedThemeIndexPath = themesCollectionView.indexPathsForSelectedItems()?.first{
                let selectedCell = themesCollectionView.cellForItemAtIndexPath(selectedThemeIndexPath) as! ThemeCollectionViewCell
               // themeVC.themeImage = selectedCell.themeImage.image
                themeVC.themeScene = profileScenes
                themeVC.themeName = selectedCell.themeName.text
            }
        }
    }
    
    
    override func scrollViewDidScroll(scrollView: UIScrollView) {
        if scrollView.isKindOfClass(UITableView){
            //scrolling the global table View
            super.scrollViewDidScroll(scrollView)

        }else if scrollView.isKindOfClass(UICollectionView){
            //scrolling the horizontal theme slider

            
        }
        
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



// MARK:: built in protocol

// MARK:: horizontal theme slider, Extension for UICollectionViewDelegate, UICollectionViewDataSource and UICollectionViewDelegateFlowLayout protocol
extension ProfileViewController: UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout{

    
    func collectionView(collectionView: UICollectionView, viewForSupplementaryElementOfKind kind: String, atIndexPath indexPath: NSIndexPath) -> UICollectionReusableView {
        let headerView = collectionView.dequeueReusableSupplementaryViewOfKind(kind, withReuseIdentifier: "AddThemeBoxIden", forIndexPath: indexPath)
        headerView.frame.size = themeImageSize
        headerView.layer.cornerRadius = StyleSchemeConstant.horizontalSlider.horizontalSliderCornerRadius
        return headerView
    }
    
    
    func scrollViewWillEndDragging(scrollView: UIScrollView, withVelocity velocity: CGPoint, targetContentOffset: UnsafeMutablePointer<CGPoint>) {
        if isUserOwnProfile{
            if scrollView.isKindOfClass(UICollectionView){
                if addThemeBoxOpen{
                    if scrollView.contentOffset.x > 0{
                        hideAddThemeBox()
                    }
                }
                else{
                    if scrollView.contentOffset.x < 70{
                        openAddThemeBox()
                    }
                }
            }
        }
    }
    
    func collectionView(collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return profileThemes.count
    }
    
    func collectionView(collectionView: UICollectionView, cellForItemAtIndexPath indexPath: NSIndexPath) -> UICollectionViewCell {
        let themeCell = collectionView.dequeueReusableCellWithReuseIdentifier(themeSlideConstant.themeCellReuseIdentifier, forIndexPath: indexPath) as! ThemeCollectionViewCell
        themeCell.layer.cornerRadius = StyleSchemeConstant.horizontalSlider.horizontalSliderCornerRadius
        themeCell.themeImage.image = UIImage(named: profileThemes[indexPath.row].imageUrl)
        let longPressGesture = UILongPressGestureRecognizer(target: self, action: #selector(ProfileViewController.themeLongPressed ))
        themeCell.themeImage.addGestureRecognizer(longPressGesture)
        themeCell.imageViewSize = themeImageSize
        themeCell.themeName.text = profileThemes[indexPath.row].themeName.uppercaseString
        themeCell.themeImage.themeName = themeCell.themeName.text
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
        return themeImageSize
    }
    
    
    //only the height width is used for horizontal slider
    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, referenceSizeForHeaderInSection section: Int) -> CGSize {
        if isUserOwnProfile{
            return themeImageSize
        }else{
            return CGSizeZero
        }
    }
    
}



// MARK:: Post Rows, Extension for UITableViewDelegate and UITableViewDataSource protocol
extension ProfileViewController: UITableViewDelegate, UITableViewDataSource{
    //defines how many weeks the profile user has
    func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        return 1
    }
    
    //each section is a collection of the same week
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return profileScenes.count
    }
    
    //define the data source for a specific week
    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell{
        let cell = tableView.dequeueReusableCellWithIdentifier("postCell", forIndexPath: indexPath) as! PostTableViewCell
        cell.scenePictureUrl = profileScenes[indexPath.row].imageUrl
        cell.themeName = profileScenes[indexPath.row].themeName
        cell.descriptionText = profileScenes[indexPath.row].postText
        cell.postUser = profileScenes[indexPath.row].postUser
        cell.postTimeText = profileScenes[indexPath.row].postTime
        return cell
    }
    
//    func tableView(tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
//        let sectionHeaderView = UITableViewHeaderFooterView()
//        //border view
//        let borderView = UIView()
//        borderView.backgroundColor = UIColor(red: 239 / 255.0, green: 239 / 255.0, blue: 244 / 255.0, alpha: 1)
//        borderView.frame = CGRect(x: 0, y: 0, width: tableView.frame.size.width, height: 1)
//       
//        //date view
//        let sectionLabel = UILabel()
//        sectionLabel.text = profileScenes[section].weekDisplayInfo
//        sectionLabel.frame = CGRect(x: 16, y: 14, width: 180, height: 18)
//        sectionLabel.font = UIFont.systemFontOfSize(13, weight: UIFontWeightMedium)
//        sectionLabel.textColor = UIColor(red: 20 / 255.0, green:  20 / 255.0, blue:  20 / 255.0, alpha: 1)
//        
//        sectionHeaderView.addSubview(borderView)
//        sectionHeaderView.addSubview(sectionLabel)
//        sectionHeaderView.contentView.backgroundColor = UIColor.whiteColor()
//
//        return sectionHeaderView
//    }
    
    func tableView(tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
        return 0
    }
    
//     func tableView(tableView: UITableView, willDisplayHeaderView view: UIView, forSection section: Int) {
//        let header: UITableViewHeaderFooterView = view as! UITableViewHeaderFooterView //recast your view as a UITableViewHeaderFooterView
//        header.contentView.backgroundColor = UIColor(red: 0/255, green: 181/255, blue: 229/255, alpha: 1.0) //make the background color light blue
//        header.textLabel!.textColor = UIColor.whiteColor() //make the text white
//        header.alpha = 0.5 //make the header transparent
//    }
//    
}

//extension ProfileViewController: UIViewControllerTransitioningDelegate{
//    func animationControllerForPresentedController(presented: UIViewController, presentingController presenting: UIViewController, sourceController source: UIViewController) -> UIViewControllerAnimatedTransitioning? {
//        closeUpTransition.selectedItemInfo = selectedThumbnailItemInfo
//        return closeUpTransition
//    }
//    
//    func animationControllerForDismissedController(dismissed: UIViewController) -> UIViewControllerAnimatedTransitioning? {
//        closeUpTransition.presenting = true
//        return closeUpTransition
//    }
//}
//



// MARK:: custom protocol
extension ProfileViewController: PostCollectionViewProtocol{
    func didTapCell(collectionView: UICollectionView, indexPath: NSIndexPath, scene: Scene, selectedItemInfo: CloseUpEffectSelectedItemInfo) {
//        self.interactingCollectionView = collectionView
        //present the sceneDetailViewController
     let sceneDetailViewController = storyboard?.instantiateViewControllerWithIdentifier("sceneDetailViewControllerIden") as! SceneDetailViewController
//          let sceneDetailViewController = sceneDetailNavigationController.viewControllers[0] as! SceneDetailViewController
//        
        sceneDetailViewController.scene = scene
//        sceneDetailViewController.transitioningDelegate = self
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



