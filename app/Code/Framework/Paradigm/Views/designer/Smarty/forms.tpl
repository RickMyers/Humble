{foreach from=$forms->fetch() item=form}
    <div class="form-select-row" onclick="Designer.form.open('{$form.id}')" onmouseover="this.style.cursor='pointer'; Designer.form.show('{$form.id}')" onmouseout="this.style.cursor='auto'" style="background-color: rgba(202,202,202,{cycle values=".2,.4"})">
        <div class="form-select-name">
            {$form.name}
        </div>
        <div class="form-select-description">
            {$form.description}
        </div>
    </div>
{/foreach}