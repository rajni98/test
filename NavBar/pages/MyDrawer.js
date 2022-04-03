import * as React from 'react';
import { View, Text, Button } from 'react-native';
import {
    createDrawerNavigator,
    DrawerContentScrollView,
    DrawerItemList,
    DrawerItem,
  } from '@react-navigation/drawer'; 
  import Feed from './Feed';
  import Notifications from './Notifications';
  import CustomDrawerContent from './CustomDrawerContent';
  const Drawer = createDrawerNavigator();
  import Icon from 'react-native-vector-icons/FontAwesome';

export default function MyDrawer() {
    return (
      <Drawer.Navigator
        drawerContent={(props) => <CustomDrawerContent {...props} />}
      >
        <Drawer.Screen name="Feed" component={Feed}  options={{
           title: 'Home',
           drawerIcon: ({focused, size}) => (
              <Icon
                 name="home"
                 size={size}
                 color={focused ? '#7cc' : '#ccc'}
              />
           ),
        }}/>
        <Drawer.Screen name="Notifications" component={Notifications} options={{
           title: 'Notifications',
           drawerIcon: ({focused, size}) => (
              <Icon
                 name="bell"
                 size={size}
                 color='black'
              />
           ),
        }}/>
      </Drawer.Navigator>
    );
  }