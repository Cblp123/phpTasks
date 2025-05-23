function find() {
    const addr = document.getElementById("address").value;
    const output = document.getElementById("output");
    
    // Показываем загрузку
    output.innerHTML = '<div class="result-card">Ищем ближайшее метро...</div>';
    
    fetch(`geocoder.php?address=${encodeURIComponent(addr)}`)
      .then(res => res.json())
      .then(data => {
        if (data.nearest_metro) {
          output.innerHTML = `
            <div class="result-card">
              <div class="metro-info">
                <div class="metro-icon">🚇</div>
                <div class="metro-details">
                  <div class="metro-name">${data.nearest_metro}</div>
                  <div class="metro-distance">${data.distance_to_metro_meters}</div>
                </div>
              </div>
            </div>
          `;
        } else {
          output.innerHTML = '<div class="result-card no-results">Ближайшее метро не найдено</div>';
        }
      })
      .catch(err => {
        output.innerHTML = '<div class="result-card no-results">Ошибка при поиске: ' + err + '</div>';
      });
}