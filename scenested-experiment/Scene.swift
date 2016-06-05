//
//  Scene.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/5/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation

class Scene{
    let id: Int
    let imageUrl: String
    let themeName: String
    let postText: String
    let postDate: String
    
    init(id: Int, imageUrl: String, themeName: String, postText: String, postDate: String){
        self.id = id
        self.imageUrl = imageUrl
        self.themeName = themeName
        self.postText = postText
        self.postDate = postDate
    }

}