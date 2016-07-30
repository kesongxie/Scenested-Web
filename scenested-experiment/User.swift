//
//  User.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/24/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation

class User{
    let id: Int
    let username: String
    let fullname: String
    let avatorUrl: String
    let coverUrl: String
    
    init(id: Int, username: String, fullname: String, avatorUrl: String, coverUrl: String){
        self.id = id
        self.username = username
        self.fullname = fullname
        self.avatorUrl = avatorUrl
        self.coverUrl = coverUrl
    }
    




}