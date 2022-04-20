// App.js
import React from 'react';
import { StyleSheet, Text, View, TouchableOpacity, Button, Image } from 'react-native';
import * as ImagePicker from "react-native-image-picker";
import { launchImageLibrary } from 'react-native-image-picker';
export default class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      resourcePath: '',
    };
  }

  // Launch Camera


  render() {
    return (
      <View style={styles.container}>
        <View style={styles.container}>
          <Image
            source={{
              uri: 'data:image/jpeg;base64,' + this.state.resourcePath.data,
            }}
            style={{ width: 100, height: 100 }}
          />
          <Image
            source={{ uri: this.state.resourcePath }}
            style={{ width: 200, height: 200 }}
          />

          <Text style={{ alignItems: 'center' }}>
            {this.state.resourcePath.uri}
          </Text>
          <TouchableOpacity style={styles.button}
            onPress={() =>
              ImagePicker.launchImageLibrary(
                {
                  mediaType: 'photo',
                  includeBase64: false,
                  maxHeight: 200,
                  maxWidth: 200,
                },
                (response) => {
                  console.log(response);
                  this.setState({ resourcePath: response.assets[0].uri });
                },
              )
            }
          >
            <Text style={styles.buttonText}>Select File</Text>
          </TouchableOpacity>

        </View>
      </View>
    );
  }
}
const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 30,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#fff'
  },
  button: {
    width: 250,
    height: 60,
    backgroundColor: '#3740ff',
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 4,
    marginBottom: 12
  },
  buttonText: {
    textAlign: 'center',
    fontSize: 15,
    color: '#fff'
  }
});