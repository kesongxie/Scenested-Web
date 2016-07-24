//
//  ColorSchemeConstant.swift
//  Scenested
//
//  Created by Xie kesong on 4/1/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit

struct StyleSchemeConstant{
    struct horizontalSlider{
        static let horizontalSliderCornerRadius: CGFloat = 3
       
        struct gradientOverlay{
            static let topColor = UIColor(red: (0/255.0), green: (0/255.0), blue: (0/255.0), alpha: 0.05) //the color starts
            static let bottomColor = UIColor(red: (0 / 255.0), green: (0 / 255.0), blue: (0 / 255.0), alpha: 1) //the color ends
            static let gradientColors: [CGColor] = [topColor.CGColor, bottomColor.CGColor]
            static let gradientLocation:[CGFloat] = [0.5, 0.9] //the portion that need to be gradient, [0.0, 1.0] means from the very top(0.0) to the very bottom(1.0), 0.4 means starts at somewhere near the middle
        }
    }
    
    static let themeColor = UIColor(red: 127/255.0, green: 27/255.0, blue: 27/255.0, alpha: 1)
    static let themeMainTextColor = UIColor(red: 20 / 255.0, green: 20 / 255.0, blue: 20 / 255.0, alpha: 1)
    
    static let linkColor = UIColor(red: 0 / 255.0, green: 53 / 255.0, blue: 105 / 255.0, alpha: 1)
    static let linkColorWhenTapped = UIColor(red: 0 / 255.0, green: 53 / 255.0, blue: 105 / 255.0, alpha: 0.6)
    struct editProfileBtn{
        static let borderWidth = buttonStyle.borderWidth
        static let cornerRadius = buttonStyle.cornerRadius
        static let borderColor = UIColor(red: 200/255.0, green: 200/255.0, blue: 200/255.0, alpha: 1).CGColor
        static let backgroundColor = UIColor(red: 240/255.0, green: 240/255.0, blue: 240/255.0, alpha: 1)
        static let font = UIFont.systemFontOfSize(13, weight: UIFontWeightSemibold)
        static let titleColor = themeMainTextColor
    }
    
    struct followBtn{
        static let borderWidth = buttonStyle.borderWidth
        static let cornerRadius = buttonStyle.cornerRadius
        static let borderColor = themeColor.CGColor
        static let backgroundColor = UIColor(red: 255/255.0, green: 255/255.0, blue: 255/255.0, alpha: 1)
        static let font = UIFont.systemFontOfSize(11, weight: UIFontWeightBold)
        static let titleColor = themeColor
        static let shadowColor = themeColor
    }

    
    
    
    
    
    struct navigationBarStyle{
        static let translucent = false
        static let MiddleItemTintColor = UIColor.whiteColor()
        
        static let titleTextAttributes = [NSForegroundColorAttributeName:  UIColor(red: 20/255.0, green: 20/255.0, blue: 20/255.0, alpha: 1)]
        static let backgroundColor = UIColor.redColor()
        
    }
    
    struct tabBarStyle{
        static let tinkColor = UIColor(red: 20/255.0, green: 20/255.0, blue: 20/255.0, alpha: 1)

        static let translucent = true
    }
    
    
    struct buttonStyle{
        static let cornerRadius:CGFloat = 5.0
        static let borderWidth: CGFloat = 1.0
    }
    
    struct profileCoverPhotoInfo{
        static let width: CGFloat = 1280
        static let height: CGFloat = 400
        static let aspectRatio: CGFloat = 3.2
    }
    
    
    
    
    
    
    
}