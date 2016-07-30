//
//  PostTableViewCell.swift
//  scenested-experiment
//
//  Created by Xie kesong on 5/3/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class PostTableViewCell: UITableViewCell {
    
    
    
    @IBOutlet weak var postUserImageView: UIImageView!{
        didSet{
            postUserImageView.layer.cornerRadius = postUserImageView.frame.size.width / 2
            postUserImageView.clipsToBounds = true
        }
    }
    
    
    @IBOutlet weak var postUserNameLabel: UILabel!
    
    
    @IBOutlet weak var themeNameLabel: UILabel!
    
    
    
    @IBOutlet weak var postTimeLabel: UILabel!
    
    @IBOutlet weak var descriptionTextView: UITextView!
    
    
    
    @IBOutlet weak var postPictureImageView: UIImageView!
    
    @IBOutlet weak var postPictureHeightConstraint: NSLayoutConstraint!
    
    
    
    
    var scenePictureUrl: String?{
        didSet{
            if let url = scenePictureUrl{
                postPictureImageView.image = UIImage(named: url)
                if let picSize = postPictureImageView.image?.size{
                    postPictureImageView.frame.size.width = UIScreen.mainScreen().bounds.size.width
                    postPictureHeightConstraint.constant = postPictureImageView.bounds.size.width * picSize.height / picSize.width
                }
            }
            
        }
    }
    
    
    var themeName: String?{
        didSet{
            themeNameLabel.text = themeName
        }
    }
    
    var descriptionText: String?{
        didSet{
            descriptionTextView.setStyleText(descriptionText!)
        }
    }
    
    var postUser: User?{
        didSet{
            postUserNameLabel.text = postUser!.fullname
            postUserImageView.image = UIImage(named: postUser!.avatorUrl)
        }
    }
    
    
    var postTimeText: String?{
        didSet{
            postTimeLabel.text = postTimeText
        }
    }

    
    
    
    
//    @IBOutlet weak var postUserImageView: UIImageView!{
//        didSet{
//            postUserImageView.layer.cornerRadius = postUserImageView.frame.size.width / 2
//            postUserImageView.clipsToBounds = true
//        }
//    }
    
    

//    @IBOutlet private weak var postCollectionView: UICollectionView!
//    
//    private var postImageSize: CGSize = CGSizeZero
//    
//    
//    @IBOutlet weak var horizontalSliderHeightConstraint: NSLayoutConstraint!
//
////    var parentTableView: UITableView?
//    
//    var postCollectionViewDelegate: PostCollectionViewProtocol?
//    
//    
//    var weekScenes:WeekScenes?
//    
//    
//    /* define the style constant for the each post slide  */
//    private struct horizontalsliderConstant{
//        struct sectionEdgeInset{
//            static let top:CGFloat = 0
//            static let left:CGFloat = 14
//            static let bottom:CGFloat = 0
//            static let right:CGFloat = 14
//        }
//        
//        //the space between each item
//        static let lineSpace: CGFloat = 7
//        static let maxVisibleThemeCount: CGFloat = 3 //the max number of theme that is allowed to display at the screen
//        static let themeImageAspectRatio:CGFloat = 1
//        static let precicitionOffset: CGFloat = 1 //prevent the height of the collectionView from less than the total of the cell height and inset during the calculation
//        static let cellReuseIdentifier: String = "postInnerCell"
//    }
//    
//    
//    override func awakeFromNib() {
//        super.awakeFromNib()
////        postCollectionView.delegate = self
////        postCollectionView.dataSource = self
//        postCollectionView.alwaysBounceHorizontal = true
//        
//        setupPostSlideCollectionView()
//    }
//
//    override func setSelected(selected: Bool, animated: Bool) {
//        super.setSelected(selected, animated: animated)
//
//        // Configure the view for the selected state
//    }
//    
//    func setupPostSlideCollectionView(){
//        //the size for the theme image
//        postImageSize.width = (UIScreen.mainScreen().bounds.size.width - horizontalsliderConstant.sectionEdgeInset.left - 2*horizontalsliderConstant.lineSpace) / horizontalsliderConstant.maxVisibleThemeCount
//        postImageSize.height = postImageSize.width / horizontalsliderConstant.themeImageAspectRatio
//        //the height for the themeCollectionView
//        horizontalSliderHeightConstraint.constant = postImageSize.height + horizontalsliderConstant.sectionEdgeInset.top + horizontalsliderConstant.sectionEdgeInset.bottom + horizontalsliderConstant.precicitionOffset
//    }
//    
//    
//    /*
//        reset the postCollectionView before the UITbaleViewCell deque and reuse, otherwise, the cell may use the content(collectionView) of the previous cell
//     */
//    override func prepareForReuse() {
//        //reset the collectionView
//        postCollectionView.reloadData()
//    }
}


//extension PostTableViewCell: UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout{
//    
//    //the number of post in each week
//    func collectionView(collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
//        return (weekScenes != nil ? (weekScenes!.numberOfScenes()) : 0)
//    }
//    
//    func collectionView(collectionView: UICollectionView, cellForItemAtIndexPath indexPath: NSIndexPath) -> UICollectionViewCell {
//        let postCell = collectionView.dequeueReusableCellWithReuseIdentifier("postInnerCell", forIndexPath: indexPath) as! PostCollectionViewCell
//        postCell.layer.cornerRadius = StyleSchemeConstant.horizontalSlider.horizontalSliderCornerRadius
//        postCell.imageView.image = UIImage(named: weekScenes!.scenes[indexPath.row].imageUrl)
////        postCell.imageViewSize = postImageSize
////        postCell.postText.text = weekScenes!.scenes[indexPath.row].postText
//        postCell.layoutIfNeeded() //re-layout
//        return postCell
//    }
//    
//    //margin for each section
//    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, insetForSectionAtIndex section: Int) -> UIEdgeInsets {
//        return UIEdgeInsets(top: horizontalsliderConstant.sectionEdgeInset.top, left: horizontalsliderConstant.sectionEdgeInset.left, bottom: horizontalsliderConstant.sectionEdgeInset.bottom, right: horizontalsliderConstant.sectionEdgeInset.right)
//    }
//    
//    
//     //if is set to horizontal scrolling, the line spacing is the space between each column
//    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, minimumLineSpacingForSectionAtIndex section: Int) -> CGFloat {
//        return horizontalsliderConstant.lineSpace
//    }
//    
//    // resize the collectionViewCell
//    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAtIndexPath indexPath: NSIndexPath) -> CGSize {
//        return postImageSize
//    }
//    
//    func collectionView(collectionView: UICollectionView, didSelectItemAtIndexPath indexPath: NSIndexPath){
//        if let scene = weekScenes?.scenes[indexPath.row]{
//            let cell = collectionView.cellForItemAtIndexPath(indexPath) as! PostCollectionViewCell
//            let thumbnailFrame = cell.superview?.convertRect(cell.frame, toView: nil)
//            
//            let thumbnailImageView = cell.imageView
//            let tImageSize = cell.imageView.image?.size
//            let aspectRatio = getAspectRatioFromSize(tImageSize!)
//            var selectedItemInfo = CloseUpEffectSelectedItemInfo()
//            selectedItemInfo.thumbnailFrame = thumbnailFrame!
//            selectedItemInfo.thumbnailImageAspectRatio = aspectRatio
//            selectedItemInfo.thumbnailImageView = thumbnailImageView
////            selectedItemInfo.selectedItemParentGlobalView = self.parentTableView!
//            //            selectedItemInfo.selectedItemParentGlobalView = self.superview
//            //           let tableViewCell = collectionView.superview?.superview as! PostTableViewCell
////           tableViewCell.index
//            
//           // self.collectionView(collectionView, cellForItemAtIndexPath: indexPath)
//            
//            self.postCollectionViewDelegate?.didTapCell(collectionView, indexPath: indexPath, scene: scene, selectedItemInfo: selectedItemInfo)
//        }
//    }
//    
//    
//    
    
//}

