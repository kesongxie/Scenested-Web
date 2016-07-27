//
//  AppConfigureConstant.swift
//  scenested-experiment
//
//  Created by Xie kesong on 7/25/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import Foundation
import CoreLocation

let APPUUID: String = "671002C3-B3A5-4639-9C42-69E868FE81B7"
let BeaconIdentifier: String = NSBundle.mainBundle().bundleIdentifier!
let APPCLBeaconRegion = CLBeaconRegion(proximityUUID: NSUUID(UUIDString: APPUUID)!, identifier: BeaconIdentifier)

