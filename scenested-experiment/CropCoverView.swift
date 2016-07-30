//
//  CropCoverView.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/27/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CropCoverView: UIView {
    var cancelBtn = UIButton()
    var doneBtn = UIButton()
    
    
    /*
    // Only override drawRect: if you perform custom drawing.
    // An empty implementation adversely affects performance during animation.
    */
    
    override func drawRect(rect: CGRect) {
        let viewWidth = self.bounds.size.width
        let viewHeight = self.bounds.size.height
        let coverVisibleWidth = viewWidth
        let coverVisibleHeight =  coverVisibleWidth / StyleSchemeConstant.profileCoverPhotoInfo.aspectRatio
        let overLayRectWidth = viewWidth
        let overLayRectHeight = (viewHeight - coverVisibleHeight) / 2
        let overlayRectSize = CGSize(width: overLayRectWidth, height: overLayRectHeight)
        
        
        
        // critical points
        
        let topOverLayRectOrigin = CGPoint(x: 0, y: 0)
        let bottomOverLayRectOrigin = CGPoint(x: 0, y: overLayRectHeight + coverVisibleHeight)
      
        
        //define top layer
        let topPath = UIBezierPath(rect: CGRect(origin: topOverLayRectOrigin, size: overlayRectSize))
        
        let layerFillColor = UIColor(red: 0 / 255.0, green: 0 / 255.0, blue: 0 / 255.0, alpha: 0.8).CGColor
        
        let topShapeLayer = CAShapeLayer()
        topShapeLayer.path = topPath.CGPath
        topShapeLayer.fillColor = layerFillColor
        
        
        // define bottom layer
        let bottomPath = UIBezierPath(rect: CGRect(origin: bottomOverLayRectOrigin, size: overlayRectSize))
        
        let bottomShapeLayer = CAShapeLayer()
        bottomShapeLayer.path = bottomPath.CGPath
        bottomShapeLayer.fillColor = layerFillColor
        
        
        //add sub-layers
        self.layer.addSublayer(topShapeLayer)
        self.layer.addSublayer(bottomShapeLayer)
        addActionView()
    }
    
    
    
    func addActionView(){
        let viewWidth = self.frame.size.width
        let topActionView = UIView(frame: CGRect(x: 0, y: 0, width: viewWidth, height: 64))
        topActionView.backgroundColor = UIColor.whiteColor()
        
        //cancel btn
        cancelBtn.frame = CGRect(x: 0, y: 29, width: 90, height: 30)
        topActionView.addSubview(cancelBtn)
        cancelBtn.setTitle("Cancel", forState: .Normal)
        cancelBtn.setTitleColor(StyleSchemeConstant.themeMainTextColor, forState: .Normal)
        cancelBtn.setTitleColor(StyleSchemeConstant.themeMainTextColor.colorWithAlphaComponent(0.6), forState: .Highlighted)
        cancelBtn.titleLabel?.font = UIFont.systemFontOfSize(17, weight: UIFontWeightRegular)
        
        
        //Done btn
        doneBtn.frame = CGRect(x: viewWidth - 90, y: 29, width: 90, height: 30)
        topActionView.addSubview(doneBtn)
        doneBtn.setTitle("Done", forState: .Normal)
        doneBtn.setTitleColor(StyleSchemeConstant.themeColor, forState: .Normal)
        doneBtn.setTitleColor(StyleSchemeConstant.themeColor.colorWithAlphaComponent(0.6), forState: .Highlighted)
        
        doneBtn.titleLabel?.font = UIFont.systemFontOfSize(17, weight: UIFontWeightMedium)
        self.addSubview(topActionView)
    }

}
