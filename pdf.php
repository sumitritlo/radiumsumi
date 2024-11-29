<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF to PNG Converter</title>
    <style>
        body {
            margin: 0;
            background: #121212;
            color: #ffffff;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .container {
            display: flex;
            min-height: 100vh;
            background: #1e1e1e;
        }
        
        .left-panel, .right-panel {
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        
        .left-panel {
            flex: 0 0 320px;
            background: #252525;
            border-right: 2px solid #363636;
            overflow-y: auto;
            height: 100vh;
        }
        
        .pdf-upload {
            display: inline-block;
            padding: 16px 22px;
            background: linear-gradient(135deg, #00c853, #009624);
            color: white;
            border-radius: 30px;
            cursor: pointer;
            border: none;
            font-size: 15px;
            font-weight: 200;
            box-shadow: 0 4px 15px rgba(0,200,83,0.3);
            transition: all 0.3s ease;
            width: 60%;
            text-align: center;
            margin-bottom: 15px;
        }
        .sdf-upload {
            display: inline-block;
            padding: 0px 0px;
            background: linear-gradient(135deg, #00c853, #009624);
            color: white;
            border-radius: 0px;
            cursor: pointer;
            border: none;
            font-size: 0px;
            font-weight: 0;
            box-shadow: 0 4px 15px rgba(0,200,83,0.3);
            transition: all 0.3s ease;
            width: 0%;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .pdf-upload:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,200,83,0.5);
            background: linear-gradient(135deg, #00e676, #00c853);
        }
        
        .pdf-thumbnails {
            display: flex;
            flex-direction: column;
            gap: 22px;
            margin-top: 35px;
            padding: 20px;
            background: #1e1e1e;
            border-radius: 15px;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .thumbnail-container {
            position: relative;
            width: 100%;
        }

        .page-number {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            z-index: 1;
        }
        
        .pdf-thumbnail {
            width: 100%;
            height: auto;
            border: 3px solid #363636;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
        }
        
        .pdf-thumbnail:hover {
            transform: scale(1.03);
            border-color: #00c853;
            box-shadow: 0 8px 24px rgba(0,200,83,0.2);
        }
        
        .right-panel {
            flex: 1;
            background: #252525;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        
        .a4-page {
            width: 325px;
            height: 1542px;
            background: #ffffff;
            border: 2px solid #363636;
            margin: 25px 0;
            position: relative;
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
            border-radius: 0px;
            overflow: hidden;
        }
        
        .download-btn {
            padding: 16px 32px;
            background: linear-gradient(135deg, #2979ff, #1565c0);
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(41,121,255,0.3);
            margin: 25px 0;
            transition: all 0.3s ease;
            width: 250px;
            text-align: center;
        }
        
        .download-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(41,121,255,0.5);
            background: linear-gradient(135deg, #448aff, #2979ff);
        }
        
        .draggable-image {
            position: absolute;
            cursor: move;
            width: 100%;
            height: 100%;
            border-radius: 8px;
        }

        .image-wrapper {
            position: absolute;
            cursor: move;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .image-wrapper.selected {
            outline: 3px solid #2979ff;
            border-radius: 8px;
        }

        .resize-handle {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #2979ff;
            border: 2px solid #fff;
            border-radius: 50%;
            transition: transform 0.2s ease;
            display: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .image-wrapper.selected .resize-handle {
            display: block;
        }

        .resize-handle:hover {
            transform: scale(1.2);
        }

        .resize-handle.nw {
            left: -7px;
            top: -7px;
            cursor: nw-resize;
        }

        .resize-handle.ne {
            right: -7px;
            top: -7px;
            cursor: ne-resize;
        }

        .resize-handle.sw {
            left: -7px;
            bottom: -7px;
            cursor: sw-resize;
        }

        .resize-handle.se {
            right: -7px;
            bottom: -7px;
            cursor: se-resize;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <label class="pdf-upload">
                Choose PDF
                <input type="file" id="pdfInput" accept=".pdf" style="display: none;">
            </label>
           <button id="confirmBtn" style="display: none;" class="sdf-upload">Confirm</button>
            <div class="pdf-thumbnails" id="pdfThumbnails"></div>
        </div>
        <div class="right-panel">
            <div class="a4-page" id="dropZone"></div>
            <button class="download-btn" id="downloadBtn">Download Image</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.worker.min.js';

        document.getElementById('pdfInput').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('confirmBtn').style.display = 'inline-block';
                const pdf = await pdfjsLib.getDocument(URL.createObjectURL(file)).promise;
                
                const thumbnailsDiv = document.getElementById('pdfThumbnails');
                thumbnailsDiv.innerHTML = '';
                
                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    const viewport = page.getViewport({scale: 0.5});
                    
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    
                    await page.render({
                        canvasContext: context,
                        viewport: viewport
                    }).promise;
                    
                    const thumbnailContainer = document.createElement('div');
                    thumbnailContainer.className = 'thumbnail-container';
                    
                    const pageNumber = document.createElement('div');
                    pageNumber.className = 'page-number';
                    pageNumber.textContent = `Page ${i}`;
                    
                    canvas.className = 'pdf-thumbnail';
                    canvas.dataset.pageNum = i;
                    
                    thumbnailContainer.appendChild(canvas);
                    thumbnailContainer.appendChild(pageNumber);
                    thumbnailsDiv.appendChild(thumbnailContainer);

                    canvas.addEventListener('click', (e) => {
                        addImageToDropZone(canvas);
                    });
                }
            }
        });

        const dropZone = document.getElementById('dropZone');
        let selectedWrapper = null;

        dropZone.addEventListener('click', (e) => {
            if (e.target === dropZone) {
                if (selectedWrapper) {
                    selectedWrapper.classList.remove('selected');
                    selectedWrapper = null;
                }
            }
        });

        function addImageToDropZone(thumbnail) {
            const wrapper = document.createElement('div');
            wrapper.className = 'image-wrapper';
            
            const img = document.createElement('img');
            img.src = thumbnail.toDataURL();
            img.className = 'draggable-image';
            
            wrapper.appendChild(img);

            // Add resize handles
            const handles = ['nw', 'ne', 'sw', 'se'];
            handles.forEach(pos => {
                const handle = document.createElement('div');
                handle.className = `resize-handle ${pos}`;
                wrapper.appendChild(handle);
            });

            // Set initial position in center of dropZone
            const dropZoneRect = dropZone.getBoundingClientRect();
            wrapper.style.left = (dropZoneRect.width / 2 - 75) + 'px';
            wrapper.style.top = (dropZoneRect.height / 2 - 100) + 'px';
            wrapper.style.width = '150px';
            wrapper.style.height = '200px';

            let isDragging = false;
            let isResizing = false;
            let currentHandle = null;
            let startX, startY, startWidth, startHeight, startLeft, startTop;

            wrapper.addEventListener('mousedown', (e) => {
                if (selectedWrapper) {
                    selectedWrapper.classList.remove('selected');
                }
                wrapper.classList.add('selected');
                selectedWrapper = wrapper;

                if (e.target.classList.contains('resize-handle')) {
                    isResizing = true;
                    currentHandle = e.target.classList[1];
                } else {
                    isDragging = true;
                }
                startX = e.clientX;
                startY = e.clientY;
                startWidth = wrapper.offsetWidth;
                startHeight = wrapper.offsetHeight;
                startLeft = wrapper.offsetLeft;
                startTop = wrapper.offsetTop;
                e.preventDefault();
            });

            document.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    
                    let newLeft = startLeft + dx;
                    let newTop = startTop + dy;
                    
                    // Boundary checks
                    newLeft = Math.max(0, Math.min(newLeft, dropZone.offsetWidth - wrapper.offsetWidth));
                    newTop = Math.max(0, Math.min(newTop, dropZone.offsetHeight - wrapper.offsetHeight));
                    
                    wrapper.style.left = newLeft + 'px';
                    wrapper.style.top = newTop + 'px';
                } else if (isResizing) {
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    
                    let newWidth = startWidth;
                    let newHeight = startHeight;
                    let newLeft = startLeft;
                    let newTop = startTop;

                    if (currentHandle.includes('e')) {
                        newWidth = startWidth + dx;
                    }
                    if (currentHandle.includes('w')) {
                        newWidth = startWidth - dx;
                        newLeft = startLeft + dx;
                    }
                    if (currentHandle.includes('s')) {
                        newHeight = startHeight + dy;
                    }
                    if (currentHandle.includes('n')) {
                        newHeight = startHeight - dy;
                        newTop = startTop + dy;
                    }

                    // Minimum size check
                    if (newWidth >= 50 && newHeight >= 50) {
                        wrapper.style.width = newWidth + 'px';
                        wrapper.style.height = newHeight + 'px';
                        wrapper.style.left = newLeft + 'px';
                        wrapper.style.top = newTop + 'px';
                    }
                }
            });

            document.addEventListener('mouseup', () => {
                isDragging = false;
                isResizing = false;
                currentHandle = null;
            });

            dropZone.appendChild(wrapper);
        }

        document.getElementById('downloadBtn').addEventListener('click', () => {
            // Temporarily hide selection styling
            const selectedElement = document.querySelector('.selected');
            if (selectedElement) {
                selectedElement.classList.remove('selected');
            }

            html2canvas(dropZone, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#FFFFFF'
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'composed-page.webp';
                canvas.toBlob(blob => {
                    link.href = URL.createObjectURL(blob);
                    link.click();
                }, 'image/webp', 1.0);

                // Restore selection styling
                if (selectedElement) {
                    selectedElement.classList.add('selected');
                }
            });
        });
    </script>
</body>
</html>
