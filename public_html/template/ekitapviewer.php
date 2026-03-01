<?php
$file = isset($_GET['file']) ? basename($_GET['file']) : '';
$filepath = "../ebook/" . $file;

if(!file_exists($filepath)){
    die("PDF bulunamadı.");
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>E-Kitap Görüntüleyici</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
<link rel="stylesheet" href="https://uzay.info/template/backgroundonly.css">
<style>
body { display: flex; flex-direction: column; align-items: center; margin: 0; padding: 0; font-family: Arial, sans-serif; user-select: none; }
#topControls, #bottomControls { margin: 10px; text-align: center; }
#viewer { display: flex; justify-content: center; gap: 10px; margin: 10px 0; flex-wrap: nowrap; perspective: 2500px; }
.page-container { position: relative; transform-style: preserve-3d; }
canvas { border: 1px solid #ccc; box-shadow: -5px 0 15px rgba(0,0,0,0.2),5px 0 15px rgba(0,0,0,0.2); background: white; cursor: grab; transition: transform 0.8s ease, box-shadow 0.8s ease, background 0.8s ease; }
canvas:active { cursor: grabbing; }
.flip-right { transform-origin: left center; animation: flipRight 0.8s forwards; }
.flip-left { transform-origin: right center; animation: flipLeft 0.8s forwards; }
@keyframes flipRight { 0% { transform: rotateY(0deg); box-shadow: -5px 0 15px rgba(0,0,0,0.2),5px 0 15px rgba(0,0,0,0.2); } 50% { transform: rotateY(-90deg); box-shadow: -20px 0 50px rgba(0,0,0,0.5); background: linear-gradient(to left,#fff 0%,#eee 100%); } 100% { transform: rotateY(-180deg); box-shadow: -5px 0 15px rgba(0,0,0,0.2),5px 0 15px rgba(0,0,0,0.2); } }
@keyframes flipLeft { 0% { transform: rotateY(0deg); box-shadow: -5px 0 15px rgba(0,0,0,0.2),5px 0 15px rgba(0,0,0,0.2); } 50% { transform: rotateY(90deg); box-shadow: 20px 0 50px rgba(0,0,0,0.5); background: linear-gradient(to right,#fff 0%,#eee 100%); } 100% { transform: rotateY(180deg); box-shadow: -5px 0 15px rgba(0,0,0,0.2),5px 0 15px rgba(0,0,0,0.2); } }
button { padding: 8px 16px; margin: 5px; border: none; border-radius: 5px; background: #007bff; color: white; cursor: pointer; }
button:disabled { background: #aaa; cursor: not-allowed; }
#pageInfo { margin-top: 5px; font-weight: bold; }
</style>
</head>
<body>

<div id="topControls">
  <button id="zoomIn">+ Yakınlaştır</button>
  <button id="zoomOut">- Uzaklaştır</button>
</div>

<div id="viewer"></div>

<div id="bottomControls">
  <button id="prev">← Önceki</button>
  <button id="next">Sonraki →</button>
  <div id="pageInfo"></div>
</div>

<script>
let pdfDoc=null
let currentPage=1
let zoomScale=1
let startX=0
let isDragging=false
const viewer=document.getElementById('viewer')
const prevBtn=document.getElementById('prev')
const nextBtn=document.getElementById('next')
const zoomInBtn=document.getElementById('zoomIn')
const zoomOutBtn=document.getElementById('zoomOut')
const pageInfo=document.getElementById('pageInfo')

const pdfPath = "<?php echo $filepath; ?>";
pdfjsLib.getDocument(pdfPath).promise.then(function(pdf){
  pdfDoc=pdf
  currentPage=1
  zoomScale=1
  renderPages()
})

function renderPages(){
  viewer.innerHTML=""
  for(let i=0;i<2;i++){
    const pageNum=currentPage+i
    if(pageNum<=pdfDoc.numPages) renderPage(pageNum)
  }
  prevBtn.disabled=currentPage===1
  nextBtn.disabled=currentPage+1>=pdfDoc.numPages
  pageInfo.textContent=`Sayfa ${currentPage}-${Math.min(currentPage+1,pdfDoc.numPages)} / ${pdfDoc.numPages}`
}
function renderPage(num){
  pdfDoc.getPage(num).then(function(page){
    const container=document.createElement("div")
    container.classList.add("page-container")
    viewer.appendChild(container)
    const canvas=document.createElement("canvas")
    container.appendChild(canvas)
    const ctx=canvas.getContext("2d")
    const viewport=page.getViewport({scale:1})
    const scale=(window.innerWidth*zoomScale)/viewport.width/2
    const scaledViewport=page.getViewport({scale})
    canvas.height=scaledViewport.height
    canvas.width=scaledViewport.width
    page.render({canvasContext:ctx,viewport:scaledViewport})
    addDragEvents(canvas)
  })
}
function addDragEvents(canvas){
  canvas.addEventListener("mousedown",(e)=>{isDragging=true; startX=e.clientX; canvas.style.cursor="grabbing"})
  canvas.addEventListener("mouseup",(e)=>{
    if(isDragging){
      let endX=e.clientX
      if(startX-endX>50) nextBtn.click()
      if(endX-startX>50) prevBtn.click()
    }
    isDragging=false; canvas.style.cursor="grab"
  })
  canvas.addEventListener("mouseleave",()=>{isDragging=false; canvas.style.cursor="grab"})
}
prevBtn.addEventListener('click',()=>{
  if(currentPage>1){ animateFlip('left'); currentPage-=2; setTimeout(renderPages,800) }
})
nextBtn.addEventListener('click',()=>{
  if(currentPage+1<pdfDoc.numPages){ animateFlip('right'); currentPage+=2; setTimeout(renderPages,800) }
})
zoomInBtn.addEventListener('click',()=>{zoomScale+=0.05; renderPages()})
zoomOutBtn.addEventListener('click',()=>{if(zoomScale>0.1){zoomScale-=0.05; renderPages()}})
document.addEventListener('keydown',(e)=>{if(pdfDoc){if(e.key==="ArrowLeft") prevBtn.click(); if(e.key==="ArrowRight") nextBtn.click()}})
viewer.addEventListener("touchstart",(e)=>{startX=e.touches[0].clientX})
viewer.addEventListener("touchend",(e)=>{
  let endX=e.changedTouches[0].clientX
  if(startX-endX>50) nextBtn.click()
  if(endX-startX>50) prevBtn.click()
})
function animateFlip(direction){
  const pages=document.querySelectorAll(".page-container")
  if(pages.length===0) return
  if(direction==='right'){ pages[1]?.classList.add("flip-right"); setTimeout(()=>{pages[1]?.classList.remove("flip-right")},800) }
  else if(direction==='left'){ pages[0]?.classList.add("flip-left"); setTimeout(()=>{pages[0]?.classList.remove("flip-left")},800) }
}
</script>
</body>
</html>
