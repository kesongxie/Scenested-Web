//
//  CropAvatorView.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/24/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

class CropAvatorView: UIView {

    /*
    // Only override drawRect: if you perform custom drawing.
    // An empty implementation adversely affects performance during animation.
    override func drawRect(rect: CGRect) {
        // Drawing code
    }
    */

    
    override func drawRect(rect: CGRect) {
        let viewWidth = self.bounds.size.width
        let viewHeight = self.bounds.size.height
        let viewWidthHalf = viewWidth / 2
        let viewHeightHalf = viewHeight / 2
        let cropImageRadius = viewWidthHalf
        
        // critical points
        let leftTopCornerPoint = CGPoint(x: 0, y: 0)
        let leftBottomCornerPoint = CGPoint(x: 0, y: viewHeight)
        let rightTopCornerPoint = CGPoint(x: viewWidth, y: 0)
        let rightBottomCornerPoint = CGPoint(x: viewWidth, y: viewHeight)
        let centerPoint = CGPoint(x: viewWidthHalf, y: viewHeightHalf)
        let middlePointRightEdge = CGPoint(x: viewWidth, y: viewHeightHalf)
        
        //define top layer
        let topPath = UIBezierPath()
        
        let layerFillColor = UIColor(red: 255 / 255.0, green: 255 / 255.0, blue: 255 / 255.0, alpha: 0.2).CGColor
        
        //origin
        topPath.moveToPoint(leftTopCornerPoint)
        topPath.addLineToPoint(rightTopCornerPoint)
        topPath.addLineToPoint(middlePointRightEdge)
        topPath.addArcWithCenter(centerPoint, radius: cropImageRadius, startAngle: 0, endAngle: CGFloat(M_PI), clockwise: false)
        topPath.closePath()
        let topShapeLayer = CAShapeLayer()
        topShapeLayer.path = topPath.CGPath
        topShapeLayer.fillColor = layerFillColor
      
        
       // define bottom layer
        let bottomPath = UIBezierPath()
        bottomPath.moveToPoint(middlePointRightEdge)
          bottomPath.addArcWithCenter(centerPoint, radius: self.bounds.size.width / 2, startAngle: 0, endAngle: CGFloat(M_PI), clockwise: true)
        bottomPath.addLineToPoint(leftBottomCornerPoint)
        bottomPath.addLineToPoint(rightBottomCornerPoint)
        bottomPath.closePath()
        
        
        
        let bottomShapeLayer = CAShapeLayer()
        bottomShapeLayer.path = bottomPath.CGPath
        bottomShapeLayer.fillColor = layerFillColor
     
        
        //add sub-layers
        self.layer.addSublayer(topShapeLayer)
        self.layer.addSublayer(bottomShapeLayer)
    }
    
}
