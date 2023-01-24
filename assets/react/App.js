import '../styles/app.css';
import React from 'react';
import ReactDOM from 'react-dom/client';
import DisplayMedia from './components/DisplayMedia';

function App() {
    return (
        <div className="App">
            <DisplayMedia />
        </div>
    );
}

/* const produit = document.querySelector('#react_component_display_media');
const getData = (image, json=true) => {
    const value = produit.getAttribute(`data-${image}`);
    return json ? JSON.parse(value) : value;
}

const element = React.createElement(DisplayMedia, {items: getData('items')});

ReactDOM.render(element, document.getElementById('react_component_display_media')); */

const root = ReactDOM.createRoot(document.getElementById('react_component_display_media'));
root.render(<App />);
//ReactDOM.render(<App />, document.getElementById('react_component_display_media'));

export default App;