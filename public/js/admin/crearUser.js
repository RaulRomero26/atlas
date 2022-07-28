//condiciones para permisos
var passButton  =   document.getElementById('id_pass_button')
var inputPass   =   document.getElementById('id_input_pass') 
var mySubmit    =   document.getElementById('mySubmit')
var myForm      =   document.getElementById('id_form')
document.getElementById('error_img1').style.display = "none"
function viewPassword(){
    if (inputPass.type == 'text') {
        inputPass.type = 'password'
    }
    else{
        inputPass.type = 'text'
    }
    
}

passButton.addEventListener('click',viewPassword)



function disablePermisos(){
    var permisos = document.getElementsByClassName('checkPermisos');
        permisos = Array.prototype.slice.call( permisos, 0 );
    
    if (document.getElementById('Modo_Admin').checked) 
        permisos.forEach(element => {element.disabled = true});
    
    else
        permisos.forEach(element => {element.disabled = false});    
}

disablePermisos()

//procesamiento de la imagen a subir
var img_1 = document.getElementById("id_foto_file")

var p_1 = document.getElementById('preview_1')

p_1.style.display = "none"

img_1.onchange = function(e) {
    let formatosImg = 'image/png image/jpeg image/jpg'

    //console.log(img_1.files[0])
    //console.log(img_1.files[0].type)
    let reader = new FileReader();
    if (typeof img_1.files[0] !== 'undefined') {
        if (img_1.files[0].size <= 8000000) { //size max 8MB
            if(formatosImg.includes(img_1.files[0].type+"")){
                document.getElementById('error_img1').style.display = "none"
                reader.onload = function() {
                    let image = document.createElement('img');

                    document.getElementById('label_foto_file').textContent = e.target.files[0].name

                    image.src = reader.result;
                    p_1.style.display = "block"
                    p_1.innerHTML = '';
                    p_1.append(image);
                    //alert('Tamaño: ' + img_1.files[0].size)
                };

                reader.readAsDataURL(e.target.files[0]);
            }
            else{
                delete img_1.files[0];
                p_1.style.display = "none"
                document.getElementById('error_img1').style.display = "block"
                img_1.value = ""
                document.getElementById('label_foto_file').textContent = "Subir imagen"
            }

        }
    } 
    else {
        delete img_1.files[0];
        p_1.style.display = "none"
        document.getElementById('error_img1').style.display = "block"
        img_1.value = ""
        document.getElementById('label_foto_file').textContent = "Subir imagen"
    }

}

/*JS para activar todos o ninguno de los permisos marcados*/

var all_juridico = document.getElementById('all_juridico')
var all_dictamen = document.getElementById('all_dictamen')
var all_remisiones = document.getElementById('all_remisiones')
var all_inteligencia = document.getElementById('all_inteligencia')
var all_inteligencia_op = document.getElementById('all_inteligencia_op')
var all_iph_final = document.getElementById('all_iph_final')
var all_corralon = document.getElementById('all_corralon')
var all_eventos_d = document.getElementById('all_eventos_d')

all_juridico.addEventListener('change',change_all)
all_dictamen.addEventListener('change',change_all)
all_remisiones.addEventListener('change',change_all)
all_inteligencia.addEventListener('change',change_all)
all_inteligencia_op.addEventListener('change',change_all)
all_iph_final.addEventListener('change',change_all)
all_corralon.addEventListener('change',change_all)
all_eventos_d.addEventListener('change',change_all)


function change_all(e){
    switch(e.target.id){
        case 'all_juridico':
            if (all_juridico.value === '1') {
                document.getElementById('Ju_Create').checked = true
                document.getElementById('Ju_Read').checked = true
                document.getElementById('Ju_Update').checked = true
                document.getElementById('Ju_Delete').checked = true
                all_juridico.value = '0'
            }
            else{
                document.getElementById('Ju_Create').checked = false
                document.getElementById('Ju_Read').checked = false
                document.getElementById('Ju_Update').checked = false
                document.getElementById('Ju_Delete').checked = false
                all_juridico.value = '1'
            }
        break
        case 'all_dictamen':
            if (all_dictamen.value === '1') {
                document.getElementById('Dic_Create').checked = true
                document.getElementById('Dic_Read').checked = true
                document.getElementById('Dic_Update').checked = true
                document.getElementById('Dic_Delete').checked = true
                all_dictamen.value = '0'
            }
            else{
                document.getElementById('Dic_Create').checked = false
                document.getElementById('Dic_Read').checked = false
                document.getElementById('Dic_Update').checked = false
                document.getElementById('Dic_Delete').checked = false
                all_dictamen.value = '1'
            }
        break
        case 'all_remisiones':
            if (all_remisiones.value === '1') {
                document.getElementById('R_Create').checked = true
                document.getElementById('R_Read').checked = true
                document.getElementById('R_Update').checked = true
                document.getElementById('R_Delete').checked = true
                all_remisiones.value = '0'
            }
            else{
                document.getElementById('R_Create').checked = false
                document.getElementById('R_Read').checked = false
                document.getElementById('R_Update').checked = false
                document.getElementById('R_Delete').checked = false
                all_remisiones.value = '1'
            }
        break
        case 'all_inteligencia':
            if (all_inteligencia.value === '1') {
                document.getElementById('Int_Create').checked = true
                document.getElementById('Int_Read').checked = true
                document.getElementById('Int_Update').checked = true
                document.getElementById('Int_Delete').checked = true
                all_inteligencia.value = '0'
            }
            else{
                document.getElementById('Int_Create').checked = false
                document.getElementById('Int_Read').checked = false
                document.getElementById('Int_Update').checked = false
                document.getElementById('Int_Delete').checked = false
                all_inteligencia.value = '1'
            }
        break
        case 'all_inteligencia_op':
            if (all_inteligencia_op.value === '1') {
                document.getElementById('IntOp_Create').checked = true
                document.getElementById('IntOp_Read').checked = true
                document.getElementById('IntOp_Update').checked = true
                document.getElementById('IntOp_Delete').checked = true
                all_inteligencia_op.value = '0'
            }
            else{
                document.getElementById('IntOp_Create').checked = false
                document.getElementById('IntOp_Read').checked = false
                document.getElementById('IntOp_Update').checked = false
                document.getElementById('IntOp_Delete').checked = false
                all_inteligencia_op.value = '1'
            }
        break
        case 'all_iph_final':
            if (all_iph_final.value === '1') {
                document.getElementById('IPH_Create').checked = true
                document.getElementById('IPH_Read').checked = true
                document.getElementById('IPH_Update').checked = true
                document.getElementById('IPH_Delete').checked = true
                all_iph_final.value = '0'
            }
            else{
                document.getElementById('IPH_Create').checked = false
                document.getElementById('IPH_Read').checked = false
                document.getElementById('IPH_Update').checked = false
                document.getElementById('IPH_Delete').checked = false
                all_iph_final.value = '1'
            }
        break
        case 'all_corralon':
            if (all_corralon.value === '1') {
                document.getElementById('Corr_Create').checked = true
                document.getElementById('Corr_Read').checked = true
                document.getElementById('Corr_Update').checked = true
                document.getElementById('Corr_Delete').checked = true
                all_corralon.value = '0'
            }
            else{
                document.getElementById('Corr_Create').checked = false
                document.getElementById('Corr_Read').checked = false
                document.getElementById('Corr_Update').checked = false
                document.getElementById('Corr_Delete').checked = false
                all_corralon.value = '1'
            }
        break
        case 'all_eventos_d':
            if (all_eventos_d.value === '1') {
                document.getElementById('E_Create').checked = true
                document.getElementById('E_Read').checked = true
                document.getElementById('E_Update').checked = true
                document.getElementById('E_Delete').checked = true
                all_eventos_d.value = '0'
            }
            else{
                document.getElementById('E_Create').checked = false
                document.getElementById('E_Read').checked = false
                document.getElementById('E_Update').checked = false
                document.getElementById('E_Delete').checked = false
                all_eventos_d.value = '1'
            }
        break
    }
}