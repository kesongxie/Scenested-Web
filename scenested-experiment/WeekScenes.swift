//
//  WeekScenes.swift
//  scenested-experiment
//
//  Created by Xie kesong on 6/5/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation

class WeekScenes{
   
    var scenes: [Scene]  //a collection of scenes in the same week
    var weekDisplayInfo: String //infomation about the week, ex. WEEK 4TH, JAN · 2016
    
    init(scenes: [Scene], weekDisplayInfo: String){
        self.scenes = scenes
        self.weekDisplayInfo = weekDisplayInfo
    }
    
    func numberOfScenes() -> Int{
        return scenes.count
    }
}