//
//  Theme.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/2/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation

class Theme{
    let id: Int
    let imageUrl: String
    let themeName: String
    let createdDate: String
    
    init(id: Int, imageUrl: String, themeName: String, createdDate: String){
        self.id = id
        self.imageUrl = imageUrl
        self.themeName = themeName
        self.createdDate = createdDate
    }
    
    
    
}