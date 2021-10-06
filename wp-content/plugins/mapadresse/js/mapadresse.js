document.addEventListener('DOMContentLoaded',function(){


    // Je récupère tous les éléments dont la classe est mapadresse
    let cartes = document.querySelectorAll('.mapadresse');

    if( cartes.length > 0 ){

        for( let i = 0; i < cartes.length; i++ ){

            let $this = cartes[i];
            let adresse = $this.dataset.adresse;
            // let adresse = $this.getAttribute('data-adresse');
            let zoom  = $this.dataset.zoom;
            let url = `https://nominatim.openstreetmap.org/search/${adresse}?format=json&addressdetails=1&limit=1&polygon_svg=1`;  
            
            fetch(url)
                .then( function(reponse){ 
                    // console.log(reponse);
                    return reponse.json();
                })
                .then(function(donnees){
                    // console.log(donnees);
                    if(donnees.length>0){

                        let lat = donnees[0].lat;
                        let lon = donnees[0].lon;
                        let macarte = L.map($this).setView([lat,lon],zoom);
                        // Ajout des fonds de carte
                        // ex: https://b.tile.openstreetmap.fr/osmfr/19/265486/180366.png
                        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',{
                            attribution : 'Mapadresse',
                            minZoom : 1,
                            maxZoom : 20
                        }).addTo(macarte);

                        L.marker([lat,lon]).addTo(macarte).bindPopup(donnees[0].display_name).openPopup();

                    }
                    else{
                        $this.innerHTML = '<p>Adresse invalide ou non trouvée</p>';
                    }
                    
                })
                .catch(function(error){
                    console.log(error);
                });   
        
        }

    }



}); // DOM chargé