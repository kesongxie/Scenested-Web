//
//  AppDelegate.swift
//  scenested-experiment
//
//  Created by Xie kesong on 4/10/16.
//  Copyright © 2016 ___Scenested___. All rights reserved.
//

import UIKit
import CoreLocation
import CoreBluetooth

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate {
    
    
    
    
   // let loggedInUser = User(id: 2, username: "nicholas", fullname: "Nicholas Tse", avatorUrl: "avator2", coverUrl: "")

    let loggedInUser = User(id: 1, username: "kesongxie", fullname: "Kesong Xie", avatorUrl: "avator", coverUrl: "")
    
    var peripheralManager: CBPeripheralManager! //act as source for beacon
   
    
    var blueToothPeripheralManager: CBPeripheralManager!
    
    var locationManager: CLLocationManager!
    
    var window: UIWindow?
    
    var loggedInUserId: Int!
    var loggedInUserName: String!
    
    func logginUser(){
        let userDefault = NSUserDefaults.standardUserDefaults()
        if userDefault.objectForKey("loggedInUserSet") == nil{
            userDefault.setObject(loggedInUser.fullname, forKey: "loggedInUserFullName")
            userDefault.setObject(loggedInUser.id, forKey: "loggedInUserFullId")

            userDefault.setBool(true, forKey: "loggedInUserSet")
            print("log in user")

        }else{
            print(userDefault.objectForKey("loggedInUserFullName"))
            print("user logged in")

        }
    }
    
    

    func application(application: UIApplication, didFinishLaunchingWithOptions launchOptions: [NSObject: AnyObject]?) -> Bool {
        
        logginUser()
        
        // Override point for customization after application launch.
        let queue = dispatch_get_global_queue(DISPATCH_QUEUE_PRIORITY_DEFAULT, 0)
        peripheralManager = CBPeripheralManager(delegate: self, queue: queue)
        blueToothPeripheralManager = CBPeripheralManager(delegate: self, queue: queue)
        locationManager = CLLocationManager()
        locationManager.delegate = self
        

        //initilize logged-in user data
        let userDefault = NSUserDefaults.standardUserDefaults()
        loggedInUserName = userDefault.objectForKey("loggedInUserFullName") as! String
        loggedInUserId = userDefault.objectForKey("loggedInUserFullId") as! Int
        
        locationManager.startMonitoringForRegion(APPCLBeaconRegion)
        return true
    }

    func applicationWillResignActive(application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
    }

    func applicationDidEnterBackground(application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
        startBcakgroundAdvertising()
    }

    func applicationWillEnterForeground(application: UIApplication) {
        // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
        startForegroundAdvertising()
    }

    func applicationDidBecomeActive(application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    }

    func applicationWillTerminate(application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
    }


}

//MARK: App acts as source
extension AppDelegate: CBPeripheralManagerDelegate{
    func startBcakgroundAdvertising(){
        peripheralManager.stopAdvertising()
        blueToothPeripheralManager.stopAdvertising()
        
        if !blueToothPeripheralManager.isAdvertising{
            let localName = loggedInUserName
            let serviceUUID = [ CBUUID(NSUUID:  NSUUID(UUIDString: APPUUID)! ) ]
            let blueToothAdvertisingData:[String: AnyObject] =
                [
                    CBAdvertisementDataLocalNameKey: localName,
                    CBAdvertisementDataServiceUUIDsKey: serviceUUID
            ]
            blueToothPeripheralManager.startAdvertising(blueToothAdvertisingData)
        }
        print("start background advertising")
    }
    
    
    func startForegroundAdvertising(){
        //stop all the previous left-over advertising
        peripheralManager?.stopAdvertising()
        blueToothPeripheralManager?.stopAdvertising()
        
        //initialize the beacon region
        if !peripheralManager.isAdvertising  {
            let region = CLBeaconRegion(proximityUUID: NSUUID(UUIDString: APPUUID)! , major:  UInt16(loggedInUserId), minor: 1, identifier: BeaconIdentifier) //major is the logged-in user's id
            let foregroundAdvertisingData = NSDictionary(dictionary: region.peripheralDataWithMeasuredPower(nil)) as? [String: AnyObject]
            peripheralManager.startAdvertising(foregroundAdvertisingData)
        }
        print("start foreground advertising")

    }
    
    func peripheralManagerDidStartAdvertising(peripheral: CBPeripheralManager, error: NSError?) {
        if error != nil{
            print(error)
        }else{
            print("start advertising")
        }
    }
    
    
    
    func peripheralManagerDidUpdateState(peripheral: CBPeripheralManager) {
        //check whether the bluetooth is powered on or not
        let userDefault = NSUserDefaults.standardUserDefaults()
        if userDefault.objectForKey(NSUserDefaultNameKey.BluetoothEnableMessagePrompted) != nil{
            startForegroundAdvertising()
        }else{
            //prompt only for the first time
            if peripheralManager != nil{
                switch peripheralManager!.state{
                case .PoweredOn:
                      startForegroundAdvertising()
                default:
                    print(peripheralManager!.state)
                    //prompt the user to turn on the bluetooth sharing
                    dispatch_async(dispatch_get_main_queue(), {
                        let alert = UIAlertController(title: "Turn on Bluetooth", message: "Turn on bluetooth to allow sharing profiles with your near-by friends", preferredStyle: .Alert)
                        
                        let dontAllowAction = UIAlertAction(title: "Don't Allow", style: .Default, handler: nil)
                        let goToSettingAction = UIAlertAction(title: "Ok", style: .Default, handler: {
                            _ in
                            if let url = NSURL(string: "prefs:root=Bluetooth"){
                                UIApplication.sharedApplication().openURL(url)
                            }
                        })
                        alert.addAction(dontAllowAction)
                        alert.addAction(goToSettingAction)
                        userDefault.setBool(true, forKey: NSUserDefaultNameKey.BluetoothEnableMessagePrompted)
                        self.window?.rootViewController?.presentViewController(alert, animated: true, completion: nil)
                    })
                }
            }
        }
    }
}

extension AppDelegate: CLLocationManagerDelegate{
    func locationManager(manager: CLLocationManager, didRangeBeacons beacons: [CLBeacon], inRegion region: CLBeaconRegion) {
        for beacon in beacons{
            print(beacon)
        }
    }
   
    func locationManager(manager: CLLocationManager, didEnterRegion region: CLRegion) {
        locationManager.startRangingBeaconsInRegion(region as! CLBeaconRegion)
        //The app is awaken for 10s
        //read the major of the beacon, check whether the user has similar theme as the current loggedin user, if it's yes, then send a push notification
    }
    
    func locationManager(manager: CLLocationManager, didExitRegion region: CLRegion) {
        print("just exit a region")
    }
}



