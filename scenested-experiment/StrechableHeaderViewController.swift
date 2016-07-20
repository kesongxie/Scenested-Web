//
//  StrechableHeaderViewController.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/22/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class StrechableHeaderViewController: MediaResponseViewController {
    
    // MARK:: required properties when this class is inherited by other sub class
   
    //the glaobal scrollView of the ViewContrioller
    var globalScrollView: UIScrollView?{
        didSet{
            self.globalScrollView!.delegate = self
            self.globalScrollView!.alwaysBounceVertical = true
        }
    }
    
    //the height of the cover after the view finish its rendering
    var coverHeight: CGFloat = 0
    
    //the ImageView that will be adjusted
    var coverImageView: UIImageView?
    
    
     // MARK:: optional properties when this class is inherited by other sub class
    
    //if the value of defaultInitialContentOffsetTop equals to the navigationBarHeight + status bar, it would allow transluent effect behind the navigation bar
    var defaultInitialContentOffsetTop: CGFloat = 0
    
    var stretchWhenContentOffsetLessThanZero: Bool = false
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    func strechCover(){
        if let scrollView = globalScrollView {
            if  !stretchWhenContentOffsetLessThanZero || scrollView.contentOffset.y + defaultInitialContentOffsetTop < 0 {
                if let coverImageView = coverImageView{
                    var coverHeaderRect = CGRect(x: 0, y: 0, width: coverImageView.bounds.width, height: coverHeight)
                    let caculatedHeight = coverHeight - scrollView.contentOffset.y - defaultInitialContentOffsetTop
                    let caculatedOrigin = scrollView.contentOffset.y + defaultInitialContentOffsetTop
                    coverHeaderRect.size.height = caculatedHeight
                    coverHeaderRect.origin.y  = caculatedOrigin
                    coverImageView.frame = coverHeaderRect
                }
            }
        }
    }
}

extension StrechableHeaderViewController: UIScrollViewDelegate{
    func scrollViewDidScroll(scrollView: UIScrollView) {
        strechCover()
    }
}



