import React, {Component} from 'react';
import { View, Text, Button } from 'react-native';
import { NavigationContainer, DrawerActions } from '@react-navigation/native';
import {
  createDrawerNavigator,
  DrawerContentScrollView,
  DrawerItemList,
  DrawerItem,
} from '@react-navigation/drawer';
import 'react-native-gesture-handler';
import SplashScreen from 'react-native-splash-screen';
import MyDrawer from './pages/MyDrawer';
import PushController from './pages/PushNotifications';


export default class app extends Component {
  componentDidMount() {
    SplashScreen.hide();
  }
  
  render() {
  return (
    
    <NavigationContainer>
      <MyDrawer />
      <PushController/>
    </NavigationContainer>
  );
}
}
