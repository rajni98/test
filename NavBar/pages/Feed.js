
import * as React from 'react';
import { View, Text, Button, StyleSheet ,ImageBackground} from 'react-native';

export default function Feed({ navigation }) {

  const image = { uri: "https://reactjs.org/logo-og.png" };
  const image2= {uri : "https://png.pngtree.com/thumb_back/fh260/background/20200714/pngtree-modern-double-color-futuristic-neon-background-image_351866.jpg"};

    return (
      <View style={styles.container}>
      <View style={styles.item1}>
      <Text style={{fontSize:20, color:"#fff"}}>Demo Text 1</Text>
      </View>
      <View style={styles.item2}>
      <Text style={{fontSize:20, color:"#fff"}}>Demo Text 1</Text>
      </View>
      <View style={styles.item3}>
      <ImageBackground source={image2} resizeMode="cover" style={styles.image2}>
      <Text style={{fontSize:20, color:"#fff",alignItems:'center',textAlign:'center'}}>Demo Text 1</Text></ImageBackground>
      </View>
      <View style={styles.item4}>
      <ImageBackground source={image} resizeMode="cover" style={styles.image}>
      <Text style={{fontSize:20, color:"#fff",textAlign:'center'}}>Demo Text 1</Text>
    </ImageBackground>
      
      </View>
      </View>
    );

    
  }

  const styles = StyleSheet.create({
    container: {
    flex:1,
    justifyContent:"center",
    backgroundColor:"#fff",
    alignItems:"stretch"
    },
    title: {
    fontSize:20,
    color:"#fff"
    },
    item1: {
    backgroundColor:"orange",
    flex:1,
    alignItems:"center",
    justifyContent:"center",
    },
    item2: {
    backgroundColor:"purple",
    flex:1,
    alignItems:"center",
    justifyContent:"center",
    },
    item3: {
    backgroundColor:"yellow",
    flex:1,
    
    },
    item4: {
    backgroundColor:"red",
    flex:1,
      textAlign:'center',
    justifyContent:"center",
    },
    image: {
      flex: 1,
      justifyContent: "center"
    },
    image2: {
      flex: 1,
      justifyContent: "center"
    },
    });