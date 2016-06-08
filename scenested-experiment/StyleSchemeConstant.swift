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
    
    static let ThemeColor = UIColor(red: 127/255.0, green: 27/255.0, blue: 27/255.0, alpha: 1)
        
        //UIColor(red: 37/255.0, green: 111/255.0, blue: 180/255.0, alpha: 1)
        //UIColor(red: 127/255.0, green: 27/255.0, blue: 27/255.0, alpha: 1)

        //UIColor(red: 0/255.0, green: 89/255.0, blue: 160/255.0, alpha: 1)
        //UIColor(red: 19/255.0, green: 89/255.0, blue: 155/255.0, alpha: 1)
       // UIColor(red: 183/255.0, green: 0, blue: 65/255.0, alpha: 1)
    //UIColor(red: 37/255.0, green: 111/255.0, blue: 180/255.0, alpha: 1)
    
    struct navigationBarStyle{
        static let translucent = false
        static let MiddleItemTintColor = ThemeColor
        
        static let titleTextAttributes = [NSForegroundColorAttributeName: ThemeColor]
    }
    
    struct tabBarStyle{
        static let tinkColor = UIColor(red: 20/255.0, green: 20/255.0, blue: 20/255.0, alpha: 1)

        static let translucent = true
    }
    
    
    struct buttonStyle{
        static let buttonCornerRadius:CGFloat = 5.0
        static let borderWidth: CGFloat = 1.0
    }
    
    
    
    
    
    
}