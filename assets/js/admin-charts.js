'use strict';
(() => {
    const source = document.getElementById('dashboard-data');
    if (!source || !window.Chart) return;
    const data = JSON.parse(source.textContent);
    const charts = [];
    function render() {
        charts.forEach(chart => chart.destroy());
        charts.length = 0;
        const dark = document.documentElement.dataset.theme === 'dark';
        const color = dark ? '#d5d5d5' : '#555';
        const line = dark ? '#f5f5f5' : '#111';
        Chart.defaults.color = color;
        Chart.defaults.borderColor = dark ? '#292929' : '#ededed';
        const base = {responsive:true,maintainAspectRatio:false,animation:!matchMedia('(prefers-reduced-motion: reduce)').matches,plugins:{legend:{display:false}}};
        charts.push(new Chart(document.getElementById('sales-chart'),{type:'line',data:{labels:data.months,datasets:[{label:'Paid revenue',data:data.sales,borderColor:line,backgroundColor:dark?'#ffffff10':'#00000008',fill:true,tension:.25,pointRadius:3}]},options:{...base,scales:{y:{beginAtZero:true}}}}));
        charts.push(new Chart(document.getElementById('orders-chart'),{type:'bar',data:{labels:['Pending','Processing','Completed','Cancelled'],datasets:[{label:'Orders',data:data.statuses,backgroundColor:['#a3a3a3','#737373','#404040','#d4d4d4'],borderRadius:4}]},options:{...base,scales:{y:{beginAtZero:true,ticks:{precision:0}},x:{display:false}}}}));
        charts.push(new Chart(document.getElementById('components-chart'),{type:'doughnut',data:{labels:data.labels,datasets:[{data:data.counts,backgroundColor:['#111','#333','#555','#777','#999','#bbb','#ddd','#888','#444'],borderColor:dark?'#141414':'#fff',borderWidth:3}]},options:{...base,cutout:'72%'}}));
    }
    render();
    new MutationObserver(render).observe(document.documentElement,{attributes:true,attributeFilter:['data-theme']});
})();
