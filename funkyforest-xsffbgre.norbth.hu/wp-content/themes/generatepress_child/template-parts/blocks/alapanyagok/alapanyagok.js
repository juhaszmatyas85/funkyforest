$(document).ready(function() {
    const alapanyagElements = $('.alapanyag');

    alapanyagElements.each(function(index, element) {
      const imageUrl = $(element).find('.bg').css('background-image').replace(/url\(['"](.+)['"]\)/, '$1');
      const image = new Image();
      image.crossOrigin = 'Anonymous';
      image.src = imageUrl;

      image.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = this.width;
        canvas.height = this.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(this, 0, 0);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
        let totalR = 0, totalG = 0, totalB = 0;

        for (let i = 0; i < imageData.length; i += 4) {
          totalR += imageData[i];
          totalG += imageData[i + 1];
          totalB += imageData[i + 2];
        }

        const avgR = Math.round(totalR / (imageData.length / 4));
        const avgG = Math.round(totalG / (imageData.length / 4));
        const avgB = Math.round(totalB / (imageData.length / 4));

        const avgColor = `rgb(${avgR}, ${avgG}, ${avgB})`;
        // Átlagszín 50%-al halványítása
        const halfTransparentAvgColor = `rgba(${avgR}, ${avgG}, ${avgB}, 0.1)`;

        // Alapanyagok szülőelemének háttere az átlagszín 50%-al halványabb verziója
        setTimeout(function() {
            $(element).css('background-color', halfTransparentAvgColor);
        }, 100); // 2000 milliszekundum = 2 másodperc

      };
    });
  });
