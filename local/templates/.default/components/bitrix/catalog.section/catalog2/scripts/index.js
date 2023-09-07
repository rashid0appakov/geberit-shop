let filterTitle = document.querySelectorAll('.filter__title--toggle');

for(let i=0; i<filterTitle.length;i++ ) {
filterTitle[i].addEventListener('click', function () {
  this.classList.toggle('closed');

});
}

let complectCardImg = document.querySelectorAll('.complect-card__img');

for(var j=0; j<complectCardImg.length;j++ ) {
  complectCardImg[j].addEventListener('mouseenter', function () {
    console.log('wejfnwefn');
  this.firstElementChild.classList.toggle('active-card-img');
  this.lastElementChild.classList.toggle('active-card-img');
  this.addEventListener('mouseleave', function () {
  this.firstElementChild.classList.add('active-card-img');
  this.lastElementChild.classList.remove('active-card-img');
  })  
});
}
let complectCardImgH = document.querySelectorAll('.complect-card__img--horizontal');

for(var j=0; j<complectCardImgH.length;j++ ) {
  complectCardImgH[j].addEventListener('mouseenter', function () {
    console.log('wejfnwefn');
  this.firstElementChild.classList.toggle('active-card-img');
  this.lastElementChild.classList.toggle('active-card-img');
  this.addEventListener('mouseleave', function () {
  this.firstElementChild.classList.add('active-card-img');
  this.lastElementChild.classList.remove('active-card-img');
  })  
});
}



$(document).ready(function(){
  $('.complect-card__slider-container--horizontal').slick();
  $('.complect-card__slider-container').slick();
});
