/*
<div>
    <div class="uk-card uk-card-default" uk-scrollspy="cls:uk-animation-fade">
        <div class="uk-card-media-top">
            <div uk-lightbox>
                <a href="${item.image}" class="hide-ext"><img class="uk-height-max-medium" src="${item.thumb}" alt="${item.title}" /></a>
            </div>
        </div>
        <div class="uk-card-body">
            <div class="uk-card-badge uk-label">${item.status}</div>
            <h3 class="uk-card-title reset-font">${item.title}</h3>
            <p>Item ID: ${item.id}</p>
            <p>${item.description}</p>
            ${item.lost_timestamp} ? <p>Lost: ${item.lost_timestamp}</p>
            ${item.found_timestamp} ? <p>Found: ${item.found_timestamp}</p>
            ${item.return_timestamp} ? <p>Return: ${item.return_timestamp}</p>
        </div>
    </div>
</div>
*/

class LostAndFound
{
    #config = {
        baseUrl: 'https://www.eurofurence.org/data/lf',
        noImageUrl: 'img/pages/lostandfound/no-photo.png'
    }
    #targetContainer = null;
    #data = null;

    constructor(targetContainer)
    {
        this.#targetContainer = targetContainer;
    }
    
    async build()
    {
        this.#data = await this.#fetch(`${this.#config.baseUrl}/data.json`);

        this.#data.data.forEach(item => {
            const outerDiv = document.createElement('div');
            
            const ukCard = document.createElement('div');
            ukCard.classList.add('uk-card', 'uk-card-default');
            ukCard.setAttribute('uk-scrollspy', 'cls:uk-animation-fade');
            outerDiv.appendChild(ukCard);

            const media = document.createElement('div');
            media.classList.add('uk-card-media-top');
            ukCard.appendChild(media);

            const lightbox = document.createElement('div');
            lightbox.setAttribute('uk-lightbox', '');
            media.appendChild(lightbox);

            const a = document.createElement('a');
            a.href = item.image? `${this.#config.baseUrl}/image/${item.image}` : '#';
            a.classList.add('hide-ext');
            lightbox.appendChild(a);

            const img = document.createElement('img');
            img.src = item.thumb? `${this.#config.baseUrl}/thumb/${item.thumb}` : this.#config.noImageUrl;
            img.classList.add('uk-height-max-medium');
            img.alt = item.title;
            a.appendChild(img);

            const body = document.createElement('div');
            body.classList.add('uk-card-body');
            ukCard.appendChild(body);

            const badge = document.createElement('div');
            badge.classList.add('uk-card-badge', 'uk-label');
            switch (item.status)
            {
                case 'L':
                    badge.innerText = 'Lost';
                    break;
                case 'F':
                    badge.innerText = 'Found';
                    break;
                default:
                    badge.innerText = 'Unknown Status';
                    break;
            }
            body.appendChild(badge);

            const h3 = document.createElement('h3');
            h3.classList.add('uk-card-title', 'reset-font');
            h3.innerText = item.title;
            body.appendChild(h3);

            const p1 = document.createElement('p');
            p1.innerText = `Item ID: ${item.id}`;
            body.appendChild(p1);

            const p2 = document.createElement('p');
            p2.innerText = item.description;
            body.appendChild(p2);

            if (item.lost_timestamp)
            {
                const plost = document.createElement('p');
                plost.innerText = `Lost: ${item.lost_timestamp}`;
                body.appendChild(plost);
            }
                
            if (item.found_timestamp)
            {
                const pfound = document.createElement('p');
                pfound.innerText = `Found: ${item.found_timestamp}`;
                body.appendChild(pfound);
            }
                
            if (item.return_timestamp)
            {
                const preturn = document.createElement('p');
                preturn.innerText = `Return: ${item.return_timestamp}`;
                body.appendChild(preturn);
            }

            this.#targetContainer.appendChild(outerDiv);
        });
    }

    async #fetch(url)
    {
        url += `?${Date.now()}`;
        var data = null;
        try
        {
            data = await (await fetch(url)).json();
            if (!data)
            {
                console.error("[eflf] malformed data loaded:", data);
                throw "malformed data";
            }
        }
        catch(ex)
        {
            console.error(`[eflf] failed to load ${url}, reason: ${ex}`);
            return;
        }
        return data;
    }
}

const lostandfound = new LostAndFound(document.getElementById('ef-lostandfound'));
lostandfound.build()
