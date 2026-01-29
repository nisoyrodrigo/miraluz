var ctx = document.getElementById("pregunta_uno");
var pregunta_uno = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
          datasets: [{
              data: [10, 20, 30]
          }],

          // These labels appear in the legend and in the tooltips when hovering different arcs
          labels: [
              'Red',
              'Yellow',
              'Blue'
          ]
        },
});

