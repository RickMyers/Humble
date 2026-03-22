{assign var=inventory value=$workflows->inventory()}
{
    "data": {
        "rows":     "{$workflows->rows()}",
        "rowCount": "{$workflows->rowCount()}",
        "page":     "{$workflows->page()}",
        "pages":    "{$workflows->pages()}",
        "fromRow":  "{$workflows->fromRow()}",
        "toRow":    "{$workflows->toRow()}"
    },
    "diagrams": {$inventory}
}
