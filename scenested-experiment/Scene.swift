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
    let postUser: User
    let imageUrl: String
    let themeName: String
    let postText: String
    let postTime: String
    
    init(id: Int, postUser: User, imageUrl: String, themeName: String, postText: String, postTime: String){
        self.id = id
        self.postUser = postUser
        self.imageUrl = imageUrl
        self.themeName = themeName
        self.postText = postText
        self.postTime = postTime
    }
    
    

}