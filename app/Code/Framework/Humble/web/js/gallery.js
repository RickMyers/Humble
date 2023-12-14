

/*
export let gallery = {
    notice: function () {
        alert('did it!');
    }
}
*/
class Gallery {
    #element    = null;
    #index      = 0;
    #animation  = {
        "slide": false,
        "fade":  true
    }
    #current    = {};
    #photos     = [];
    #last       = {};
    
    constructor(id) {
        this.set(id);
    }
    
    set(id) {
        if (id) {
            this.#element = document.getElementById(id);
            this.stage();
        }
    }
    
    add(image,caption) {
        if (image && caption) {
            this.#photos[this.#photos.length] = {
                "image": image,
                "caption": caption
            }
            console.log(this.#element);
            let node = this.#element.appendChild('img');
            node.addAttribute('src',image);
            node.addAttribute('title',caption);
            node.addAttribute('width','100%');
            node.addAttribute('height','100%');
        }
    }
    
    next() {
        this.#last = this.#current;
        if (++$this.#index > this.#photos.length) {
            this.#index = 0;
        }
        this.#current = this.#photos[this.#index];
        this.render();
    }
    
    last() {
        this.#last = this.#current;
        if (++$this.#index > this.#photos.length) {
            this.#index = 0;
        }
        this.#current = this.#photos[this.#index];
    }
    
    stage() {
        //sets the controls and injects the two frames, one for the next image, one for the current
    }
    
    render() {
        //actually does the fade in and fade out of both frames
    }
    hi() {
        alert('hello again');
    }
}
export let gallery = new Gallery();