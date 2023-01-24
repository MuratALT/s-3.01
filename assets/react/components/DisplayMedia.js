import React from "react";
import "react-responsive-carousel/lib/styles/carousel.min.css"; // requires a loader
import { Carousel } from 'react-responsive-carousel';
import "../../styles/DisplayMedia.css";
import { data } from "jquery";

function DisplayMedia() {
    /* const $url = "../../uploads/product_directory/" */
    /* const $url = "../uploads/product_directory/" */

    let product = document.querySelector('#react_component_display_media');
    let img_name = JSON.parse(product.dataset.imgname);
    const $url = product.dataset.url;


    const res = [];
    let k = 0;
    img_name.forEach(element => {
        // On va créer un objet avec un id et l'image
        let obj = {
            id: k,
            image: $url + element
        }
        k++;
        res.push(obj);
    });

    return (
        <div>
            <Carousel 
                /* Pour activer l'auto lecture en boucle avec une intervalle de 10 secondes */
                autoPlay
                interval={10000} 
                infiniteLoop 
                
               /* thumbWidth={100} */

                /* Pour activer seulement les cercles de navigation */
                showIndicators={true} showThumbs={false}

                /* Pour activer seulement l'aperçu des images */
               // showIndicators={false} showThumbs={true}

                /* Pour désactiver le statut de navigation */
                showStatus={false}
                
                
            >
                {res.map((slide) => (
                    <div key={slide.id}>
                        <img width={"300px"} src={slide.image} alt="" />
                        <div className="overlay">
                            {/* <h2 className="overlay_title">{slide.title}</h2>
                            <p className="overlay_text">{slide.text}</p> */}
                        </div>
                    </div>
                ))}
            </Carousel>
        </div>
    );
}

export default DisplayMedia;