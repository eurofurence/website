const regStatConfig = {
    year: new Date().getFullYear(),
    // data: 'https://www.eurofurence.org/data/reg/{year}.json',
    // data: 'https://wwwtest.eurofurence.org/data/reg/{year}.json',
    data: 'http://localhost/_workfiles/temp/{year}.json'
}

class RegStats
{
    colors = {
        ci: {
            100: '#005953',
            80: '#338C86',
            60: '#66BFB9',
            50: '#7FD8D2',
            40: '#99F2EC',
            20: '#CCFFFF'
        },
        lanyard: {
            normal: '#49854b',
            sponsor: '#f5ca20',
            supersponsor: '#83559e',
            staff: '#e37529',
            director: '#dc333d'
        },
        interests: {
            animation: '#be89f3',
            art: '#96c6fb',
            fursuiting: '#ff9d08',
            music: '#08e5bd'
        }
    };
    countryCodes = {
        AD: "Andorra",
        AE: "United Arab Emirates",
        AF: "Afghanistan",
        AG: "Antigua And Barbuda",
        AI: "Anguilla",
        AL: "Albania",
        AM: "Armenia",
        AN: "Netherlands Antilles",
        AO: "Angola",
        AQ: "Antarctica",
        AR: "Argentina",
        AS: "American Samoa",
        AT: "Austria",
        AU: "Australia",
        AW: "Aruba",
        AX: "Aland Islands",
        AZ: "Azerbaijan",
        BA: "Bosnia And Herzegovina",
        BB: "Barbados",
        BD: "Bangladesh",
        BE: "Belgium",
        BF: "Burkina Faso",
        BG: "Bulgaria",
        BH: "Bahrain",
        BI: "Burundi",
        BJ: "Benin",
        BL: "Saint Barthelemy",
        BM: "Bermuda",
        BN: "Brunei Darussalam",
        BO: "Bolivia",
        BQ: "Bonaire, Sint Eustatius and Saba",
        BR: "Brazil",
        BS: "Bahamas",
        BT: "Bhutan",
        BV: "Bouvet Island",
        BW: "Botswana",
        BY: "Belarus",
        BZ: "Belize",
        CA: "Canada",
        CC: "Cocos (Keeling) Islands",
        CD: "Congo, Democratic Republic",
        CF: "Central African Republic",
        CG: "Congo",
        CH: "Switzerland",
        CI: "Cote D\"Ivoire",
        CK: "Cook Islands",
        CL: "Chile",
        CM: "Cameroon",
        CN: "China",
        CO: "Colombia",
        CR: "Costa Rica",
        CU: "Cuba",
        CV: "Cape Verde",
        CW: "The Country of Curaçao",
        CX: "Christmas Island",
        CY: "Cyprus",
        CZ: "Czech Republic",
        DE: "Germany",
        DJ: "Djibouti",
        DK: "Denmark",
        DM: "Dominica",
        DO: "Dominican Republic",
        DZ: "Algeria",
        EC: "Ecuador",
        EE: "Estonia",
        EG: "Egypt",
        EH: "Western Sahara",
        ER: "Eritrea",
        ES: "Spain",
        ET: "Ethiopia",
        FI: "Finland",
        FJ: "Fiji",
        FK: "Falkland Islands (Malvinas)",
        FM: "Micronesia, Federated States Of",
        FO: "Faroe Islands",
        FR: "France",
        GA: "Gabon",
        GB: "United Kingdom",
        GD: "Grenada",
        GE: "Georgia",
        GF: "French Guiana",
        GG: "Guernsey",
        GH: "Ghana",
        GI: "Gibraltar",
        GL: "Greenland",
        GM: "Gambia",
        GN: "Guinea",
        GP: "Guadeloupe",
        GQ: "Equatorial Guinea",
        GR: "Greece",
        GS: "South Georgia And Sandwich Isl.",
        GT: "Guatemala",
        GU: "Guam",
        GW: "Guinea-Bissau",
        GY: "Guyana",
        HK: "Hong Kong",
        HM: "Heard Island & Mcdonald Islands",
        HN: "Honduras",
        HR: "Croatia",
        HT: "Haiti",
        HU: "Hungary",
        ID: "Indonesia",
        IE: "Ireland",
        IL: "Israel",
        IM: "Isle Of Man",
        IN: "India",
        IO: "British Indian Ocean Territory",
        IQ: "Iraq",
        IR: "Iran, Islamic Republic Of",
        IS: "Iceland",
        IT: "Italy",
        JE: "Jersey",
        JM: "Jamaica",
        JO: "Jordan",
        JP: "Japan",
        KE: "Kenya",
        KG: "Kyrgyzstan",
        KH: "Cambodia",
        KI: "Kiribati",
        KM: "Comoros",
        KN: "Saint Kitts And Nevis",
        KP: "North Korea",
        KR: "Korea",
        KW: "Kuwait",
        KY: "Cayman Islands",
        KZ: "Kazakhstan",
        LA: "Lao People\"s Democratic Republic",
        LB: "Lebanon",
        LC: "Saint Lucia",
        LI: "Liechtenstein",
        LK: "Sri Lanka",
        LR: "Liberia",
        LS: "Lesotho",
        LT: "Lithuania",
        LU: "Luxembourg",
        LV: "Latvia",
        LY: "Libyan Arab Jamahiriya",
        MA: "Morocco",
        MC: "Monaco",
        MD: "Moldova",
        ME: "Montenegro",
        MF: "Saint Martin",
        MG: "Madagascar",
        MH: "Marshall Islands",
        MK: "Macedonia",
        ML: "Mali",
        MM: "Myanmar",
        MN: "Mongolia",
        MO: "Macao",
        MP: "Northern Mariana Islands",
        MQ: "Martinique",
        MR: "Mauritania",
        MS: "Montserrat",
        MT: "Malta",
        MU: "Mauritius",
        MV: "Maldives",
        MW: "Malawi",
        MX: "Mexico",
        MY: "Malaysia",
        MZ: "Mozambique",
        NA: "Namibia",
        NC: "New Caledonia",
        NE: "Niger",
        NF: "Norfolk Island",
        NG: "Nigeria",
        NI: "Nicaragua",
        NL: "Netherlands",
        NO: "Norway",
        NP: "Nepal",
        NR: "Nauru",
        NU: "Niue",
        NZ: "New Zealand",
        OM: "Oman",
        PA: "Panama",
        PE: "Peru",
        PF: "French Polynesia",
        PG: "Papua New Guinea",
        PH: "Philippines",
        PK: "Pakistan",
        PL: "Poland",
        PM: "Saint Pierre And Miquelon",
        PN: "Pitcairn",
        PR: "Puerto Rico",
        PS: "Palestinian Territory, Occupied",
        PT: "Portugal",
        PW: "Palau",
        PY: "Paraguay",
        QA: "Qatar",
        RE: "Reunion",
        RO: "Romania",
        RS: "Serbia",
        RU: "Russian Federation",
        RW: "Rwanda",
        SA: "Saudi Arabia",
        SB: "Solomon Islands",
        SC: "Seychelles",
        SD: "Sudan",
        SE: "Sweden",
        SG: "Singapore",
        SH: "Saint Helena",
        SI: "Slovenia",
        SJ: "Svalbard And Jan Mayen",
        SK: "Slovakia",
        SL: "Sierra Leone",
        SM: "San Marino",
        SN: "Senegal",
        SO: "Somalia",
        SR: "Suriname",
        SS: "The Republic of South Sudan",
        ST: "Sao Tome And Principe",
        SV: "El Salvador",
        SX: "Sint Maarten",
        SY: "Syrian Arab Republic",
        SZ: "Swaziland",
        TC: "Turks And Caicos Islands",
        TD: "Chad",
        TF: "French Southern Territories",
        TG: "Togo",
        TH: "Thailand",
        TJ: "Tajikistan",
        TK: "Tokelau",
        TL: "Timor-Leste",
        TM: "Turkmenistan",
        TN: "Tunisia",
        TO: "Tonga",
        TR: "Turkey",
        TT: "Trinidad And Tobago",
        TV: "Tuvalu",
        TW: "Taiwan",
        TZ: "Tanzania",
        UA: "Ukraine",
        UG: "Uganda",
        UM: "United States Outlying Islands",
        US: "United States",
        UY: "Uruguay",
        UZ: "Uzbekistan",
        VA: "Holy See (Vatican City State)",
        VC: "Saint Vincent And Grenadines",
        VE: "Venezuela",
        VG: "Virgin Islands, British",
        VI: "Virgin Islands, U.S.",
        VN: "Vietnam",
        VU: "Vanuatu",
        WF: "Wallis And Futuna",
        WS: "Samoa",
        XK: "Kosovo",
        YE: "Yemen",
        YT: "Mayotte",
        ZA: "South Africa",
        ZM: "Zambia",
        ZW: "Zimbabwe"
    };
    
    
    /**
     * @param {String} datasource // json url, {year} is substituted by update()
     */
    constructor(datasource)
    {
        this.datasource = datasource;
    }

    async init()
    {
        this.data = {
            year: 0,
            convention: "",
            totalcount: 0,
            age: {},
            gender: {},
            status: {},
            shirtsize: {},
            country: {},
            sponsor: {},
            specialinterest: {
                animator: 0,
                artist: 0,
                fursuiter: 0,
                musician: 0
            }
        }

        this.charts = {
            "status": this.initStatus(),
            "types": this.initTypes(),
            "age": this.initAge(),
            // "countryChart": this.initCountryChart(),
            "size": this.initSize()
        }
        
        this.timestampContainer = document.getElementById('ef-rs-timestamp');
        this.rawContainer = document.getElementById('ef-rs-data-raw');
    }

    async update(year)
    {
        this.data = await this.fetch(year);

        this.updateTotal();
        this.updateInterests();
        this.updateStatus();
        this.updateTypes();
        this.updateAge();
        this.updateCountryList();
        this.updateSize();
        
        this.timestampContainer.innerText = this.data.lastchangedatetimeutc;
        this.rawContainer.innerText = JSON.stringify(this.data, null, 4);
        if (document.getElementById('ef-rs-intro-timestamp'))
            document.getElementById('ef-rs-intro-timestamp').innerText = this.data.lastchangedatetimeutc;
    }

    updateTotal()
    {
        // total registrations
        document.getElementById('ef-rs-reg-total').innerText = this.data.totalcount;
        if (document.getElementById('ef-rs-intro-total'))
            document.getElementById('ef-rs-intro-total').innerText = this.data.totalcount;
        
        // reg open indicator
        const text = document.getElementById('ef-rs-reg-opening');
        const indicator = document.getElementById('ef-rs-reg-opening-indicator');

        if (this.data.open)
        {
            indicator.style.color = this.colors.lanyard.normal;
            indicator.classList.add('ef-rs-reg-opening-indicator-animation');
            text.innerHTML = '<a href="https://identity.eurofurence.org" target="_blank">Online registrations open</a>';
        }
        else {
            indicator.style.color = this.colors.lanyard.director;
            indicator.classList.remove('ef-rs-reg-opening-indicator-animation');
            text.innerText = 'Online registrations closed';
        }
    }

    initStatus()
    {
        return new Chart(document.getElementById('ef-rs-reg-status'),
        {
            type: 'doughnut',
            data: {
                labels:
                [
                    "New",
                    "Approved",
                    // "Partially paid",
                    "Paid",
                    "Checked in",
                    // "Cancelled",
                    // "Waiting"
                ],
                datasets: [
                    {
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            this.colors.ci[20], // new,
                            this.colors.ci[40], // approved,
                            // partially paid,
                            this.colors.ci[60], // paid,
                            this.colors.ci[100], // checked in,
                            // cancelled,
                            // waiting
                        ]
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    htmlLegend: {
                        containerID: 'ef-rs-reg-status-legend',
                        values: [0, 0, 0, 0]
                    }
                },
                events: []
            },
            plugins: [htmlLegendPlugin]
        });
    }

    updateStatus()
    {
        const values = [
            this.data.status.new || 0,
            this.data.status.approved || 0,
            (this.data.status['partially paid'] || 0)
            + (this.data.status.paid || 0),
            this.data.status['checked in'] || 0,
            // this.data.status.cancelled || 0,
            // this.data.status.waiting || 0
        ];
        
        this.charts.status.data.datasets[0].data = values;
        this.charts.status.options.plugins.htmlLegend.values = values;

        this.charts.status.update();

        if (document.getElementById('ef-rs-intro-new'))
            document.getElementById('ef-rs-intro-new').innerText = this.data.status.new || 0;
        if (document.getElementById('ef-rs-intro-approved'))
            document.getElementById('ef-rs-intro-approved').innerText = this.data.status.approved || 0;
        if (document.getElementById('ef-rs-intro-partially-paid'))
            document.getElementById('ef-rs-intro-partially-paid').innerText = this.data['partially paid'] || 0;
        if (document.getElementById('ef-rs-intro-paid'))
            document.getElementById('ef-rs-intro-paid').innerText = (this.data.status['partially paid'] || 0) + (this.data.status.paid || 0);
        if (document.getElementById('ef-rs-intro-checked-in'))
            document.getElementById('ef-rs-intro-checked-in').innerText = this.data.status['checked in'] || 0;
    }

    initTypes()
    {
        return new Chart(document.getElementById('ef-rs-reg-types'),
        {
            type: 'doughnut',
            data: {
                labels: [
                    "Attendee",
                    "Sponsor",
                    "Super Sponsor"
                ],
                datasets: [
                    {
                        data: [0, 0, 0],
                        backgroundColor: [
                            this.colors.lanyard.normal,
                            this.colors.lanyard.sponsor,
                            this.colors.lanyard.supersponsor
                        ]
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    htmlLegend: {
                        containerID: 'ef-rs-reg-types-legend',
                        values: [0, 0, 0]
                    }
                },
                events: []
            },
            plugins: [htmlLegendPlugin]
        });
    }

    updateTypes()
    {
        const values = [
            this.data.sponsor.normal || 0,
            this.data.sponsor.sponsor || 0,
            this.data.sponsor.supersponsor || 0
        ];

        this.charts.types.data.datasets[0].data = values;
        this.charts.types.options.plugins.htmlLegend.values = values;

        this.charts.types.update();
    }

    updateInterests()
    {
        document.getElementById('ef-rs-reg-interests-animation').innerText = this.data.specialinterest.animator;
        document.getElementById('ef-rs-reg-interests-art').innerText = this.data.specialinterest.artist;
        document.getElementById('ef-rs-reg-interests-fursuiting').innerText = this.data.specialinterest.fursuiter;
        document.getElementById('ef-rs-reg-interests-music').innerText = this.data.specialinterest.musician;
    }

    initAge()
    {
        return new Chart(document.getElementById('ef-rs-age'),
        {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        data: [],
                        backgroundColor: []
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    updateAge()
    {
        const values = Object.values(this.data.age);
        const vhighi  = values.indexOf(Math.max(...values));

        this.charts.age.data.labels = Object.keys(this.data.age);
        this.charts.age.data.datasets[0].data = values;
        this.charts.age.data.datasets[0].backgroundColor = this.gradient(
            this.colors.ci[100],
            this.colors.ci[20],
            values.length,
            vhighi
        );

        this.charts.age.update();

        let wsum = 0;
        for (const [age, n] of Object.entries(this.data.age))
            wsum += age * n;
        const avg = Math.round(wsum / this.data.totalcount);

        document.getElementById('ef-rs-age-avg').innerText = avg;     
    }

    initCountryChart()
    {
        document.getElementById('ef-rs-country-zoom').addEventListener('click', () => {
            // undefined == auto
            if (this.charts.countryChart.options.scales.y.max == undefined) 
            {
                const values = Object.values(this.data.country);
                const vhighi = values.indexOf(Math.max(...values));
                
                // remove highest value, so the 2nd takes its place
                values[vhighi] = 0; 

                // add 20% ontop of max value and round to steps of 10
                this.charts.countryChart.options.scales.y.max =
                    Math.ceil(Math.max(...values) * 1.2 / 10) * 10;
            }
            else
            {
                // let chartjs handle y axis scale automatically
                this.charts.countryChart.options.scales.y.max = undefined;
            }
            
            this.charts.countryChart.update();
        });

        return new Chart(document.getElementById('ef-rs-country-chart'),
        {
            type: 'bar',
            data:  {
                labels: [],
                datasets: [
                    {
                        data: [],
                        backgroundColor: this.colors.ci[60]
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    updateCountryList()
    {
        const parent = document.getElementById('ef-rs-country-list');
        parent.innerText = '';
        for (const i in this.data.country) { // iso3166 alpha-2 country code
            const n = this.countryCodes[i.toUpperCase()]; // full name
            const c = this.data.country[i]; // attendee count
            parent.innerHTML += `<article data-iso="${i}" data-name="${n}" data-count="${c}"><div class="${i.toUpperCase()}"></div><h4>${i.toUpperCase()}</h4> ${n}<span>${c}</span></article>`
        }
    }
    
    initSize()
    {
        return new Chart(document.getElementById('ef-rs-reg-size'),
        {
            type: 'doughnut',
            data: {
                labels: ['XS', 'S', 'M', 'L', 'XL', 'XXL+'/*, 'None'*/],
                datasets: [
                    {
                        data: [0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: [
                            this.colors.ci[20],
                            this.colors.ci[40],
                            this.colors.ci[50],
                            this.colors.ci[60],
                            this.colors.ci[80],
                            this.colors.ci[100]
                            // '#dddddd' // for 'None'
                        ]
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    htmlLegend: {
                        containerID: 'ef-rs-reg-size-legend',
                        values: [0, 0, 0, 0, 0, 0, 0],
                        columns: 2
                    }
                },
                events: []
            },
            plugins: [htmlLegendPlugin]
        });
    }

    updateSize()
    {
        const values = Object.values(this.data.shirtsize);

        // const none = this.data.totalcount - values.reduce((s, a) => s + a, 0);
        // this.charts.size.data.datasets[0].data = [...values, none];
        // this.charts.size.options.plugins.htmlLegend.values = [...values, none];

        this.charts.size.data.datasets[0].data = values;
        this.charts.size.options.plugins.htmlLegend.values = values;

        this.charts.size.update();
    }

    async fetch(year)
    {
        let url = this.datasource.replace('{year}', year);

        // prevent caching for live data
        url += `?${Date.now()}`

        var data = null;

        try {
            data = await (await fetch(url)).json();
            if (!data)
            {
                console.info("[ef-regstats] data", data);
                throw "malformed data";
            }
        }
        catch(ex)
        {
            console.error(`[ef-regstats] failed to load ${url}, reason: ${ex}`);
            return;
        }

        return data;
    }

    /**
     * @param {String} peak     // darkest color
     * @param {String} end      // lightest color
     * @param {Number} steps    // number of bars
     * @param {Number} center   // index of highest bar
     * @return {Array<String>}  // array of colors
     */
    gradient(peak, end, steps, center = 0)
    {
        end = this.hexToRgb(end);
        peak = this.hexToRgb(peak);

        // left part gradient including center
        let a = [];
        for (let i = steps - center - 1; i < steps; i++)
        {
            a.push(
                this.rgbToHex(
                    this.interp(end.r, peak.r, steps, i),
                    this.interp(end.g, peak.g, steps, i),
                    this.interp(end.b, peak.b, steps, i)
                )
            );
        }

        // right part
        let b = [];
        for (let i = 1; i < steps - center; i++)
        {
            b.push(
                this.rgbToHex(
                    this.interp(peak.r, end.r, steps, i),
                    this.interp(peak.g, end.g, steps, i),
                    this.interp(peak.b, end.b, steps, i)
                )
            );
        }

        return [...a, ...b];
    }

    interp(start, end, steps, step)
    {
        return Math.round(start + ((end - start) / (steps - 1)) * step);
    }

    hexToRgb(hex)
    {
        return {
            r: parseInt(hex[1] + hex[2], 16),
            g: parseInt(hex[3] + hex[4], 16),
            b: parseInt(hex[5] + hex[6], 16)
        };
    }

    rgbToHex(r, g, b)
    {
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }
}

const htmlLegendPlugin = {
    id: 'htmlLegend',
    afterUpdate(chart, args, options)
    {
        const container = document.getElementById(options.containerID);

        // Remove old legend items
        while(container.firstChild)
            container.firstChild.remove();

        // reuse the built-in legendItems generator
        const items = chart.options.plugins.legend.labels.generateLabels(chart);

        let numColumns = chart.options.plugins.htmlLegend.columns || 1;
        
        // if numColumns > 1, run this code twice to create a single column view
        for (let tablev = 0; tablev < Math.min(numColumns, 2); tablev++)
        {
            const table = document.createElement('table');

            if (numColumns > 1)
            {
                switch (tablev) {
                    case 0: // original table version with numColumns
                        table.classList.add('uk-visible@s');
                        break;
                    case 1: // second table version with one column
                        table.classList.add('uk-hidden@s');
                        numColumns = 1;
                        break;
                }
            }

            const numRows = Math.ceil(items.length / numColumns);

            for (let row = 0; row < numRows; row++)
            {
                const tr = document.createElement('tr');
                
                for (let col = 0; col < numColumns; col++)
                {
                    const i = row + col * numRows; // determine current item index
                    if (i < items.length)
                    {
                        const item = items[i];

                        // color box
                        const td1 = document.createElement('td');
                        td1.onclick = () => {
                            chart.toggleDataVisibility(item.index);
                            chart.update();
                        };
                        const div = document.createElement('div');
                        div.style.background = item.fillStyle;
                        div.style.borderColor = item.strokeStyle;
                        div.style.borderWidth = item.lineWidth + 'px';
                        td1.appendChild(div);
                        tr.appendChild(td1);

                        // value
                        const td2 = document.createElement('td');
                        td2.onclick = () => {
                            chart.toggleDataVisibility(item.index);
                            chart.update();
                        };
                        const value = document.createElement('span');
                        value.style.color = item.fontColor;
                        value.style.textDecoration = item.hidden ? 'line-through' : '';
                        value.innerText = chart.options.plugins.htmlLegend.values[i];
                        td2.appendChild(value);
                        tr.appendChild(td2);

                        // text
                        const td3 = document.createElement('td');
                        td3.onclick = () => {
                            chart.toggleDataVisibility(item.index);
                            chart.update();
                        };
                        const text = document.createElement('span');
                        text.style.color = item.fontColor;
                        text.style.textDecoration = item.hidden ? 'line-through' : '';
                        text.innerText = item.text;
                        td3.appendChild(text);
                        tr.appendChild(td3);
                    }
                }

                table.appendChild(tr);
            }

            container.appendChild(table);
        }
    }
};

const regstats = new RegStats(regStatConfig.data);

regstats.init();
regstats.update(regStatConfig.year);

const timer_set = 10; // seconds
var timer_cur = timer_set;
const timer_div = document.getElementById('ef-rs-update');

setInterval(() => {
    if (--timer_cur < 0)
    {
        regstats.update(regStatConfig.year);
        timer_cur = timer_set;
    }
    timer_div.innerText = timer_cur;
}, 1000);