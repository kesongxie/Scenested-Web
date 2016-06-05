//
//  PostTableViewCell.swift
//  scenested-experiment
//
//  Created by Xie kesong on 5/3/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class PostTableViewCell: UITableViewCell {


    @IBOutlet private weak var postCollectionView: UICollectionView!
    
    private var postImageSize: CGSize = CGSizeZero
    
    
    @IBOutlet weak var horizontalSliderHeightConstraint: NSLayoutConstraint!

    
    var postCollectionViewDelegate: PostCollectionViewProtocol?
    
    
    var weekScenes:WeekScenes?
    
    /* define the style constant for the each post slide  */
    private struct horizontalsliderConstant{
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
        static let cellReuseIdentifier: String = "postInnerCell"
    }
    
//    let postImages: [String] = ["cover3", "cover", "cover4"]
//
//    let postTexts: [String] = ["This is my first coustic fingerstyle guitar concert in New York", "Glad to see this year US Open Final", "My first hackathon ever!"]
    
    
    override func awakeFromNib() {
        super.awakeFromNib()
        postCollectionView.delegate = self
        postCollectionView.dataSource = self
        postCollectionView.alwaysBounceHorizontal = true
        
        setupPostSlideCollectionView()
    }

    override func setSelected(selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
    func setupPostSlideCollectionView(){
        //the size for the theme image
        postImageSize.width = (UIScreen.mainScreen().bounds.size.width - horizontalsliderConstant.sectionEdgeInset.left - 2*horizontalsliderConstant.lineSpace) / horizontalsliderConstant.maxVisibleThemeCount
        postImageSize.height = postImageSize.width / horizontalsliderConstant.themeImageAspectRatio
        //the height for the themeCollectionView
        horizontalSliderHeightConstraint.constant = postImageSize.height + horizontalsliderConstant.sectionEdgeInset.top + horizontalsliderConstant.sectionEdgeInset.bottom + horizontalsliderConstant.precicitionOffset
    }
}


extension PostTableViewCell: UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout{
    
    //the number of post in each week
    func collectionView(collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return (weekScenes != nil ? (weekScenes!.numberOfScenes()) : 0)
    }
    
    func collectionView(collectionView: UICollectionView, cellForItemAtIndexPath indexPath: NSIndexPath) -> UICollectionViewCell {
        let postCell = collectionView.dequeueReusableCellWithReuseIdentifier("postInnerCell", forIndexPath: indexPath) as! PostCollectionViewCell
        postCell.layer.cornerRadius = StyleSchemeConstant.horizontalSlider.horizontalSliderCornerRadius
        postCell.imageView.image = UIImage(named: weekScenes!.scenes[indexPath.row].imageUrl)
        postCell.imageViewSize = postImageSize
        postCell.postText.text = weekScenes!.scenes[indexPath.row].postText
        postCell.layoutIfNeeded() //re-layout
        return postCell
    }
    
    //margin for each section
    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, insetForSectionAtIndex section: Int) -> UIEdgeInsets {
        return UIEdgeInsets(top: horizontalsliderConstant.sectionEdgeInset.top, left: horizontalsliderConstant.sectionEdgeInset.left, bottom: horizontalsliderConstant.sectionEdgeInset.bottom, right: horizontalsliderConstant.sectionEdgeInset.right)
    }
    
    
     //if is set to horizontal scrolling, the line spacing is the space between each column
    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, minimumLineSpacingForSectionAtIndex section: Int) -> CGFloat {
        return horizontalsliderConstant.lineSpace
    }
    
    // resize the collectionViewCell
    func collectionView(collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAtIndexPath indexPath: NSIndexPath) -> CGSize {
        return postImageSize
    }
    
    func collectionView(collectionView: UICollectionView, didSelectItemAtIndexPath indexPath: NSIndexPath){
        if let scene = weekScenes?.scenes[indexPath.row]{
            self.postCollectionViewDelegate?.didTapCell(collectionView, indexPath: indexPath, scene: scene)
        }
    }
    
    
}

