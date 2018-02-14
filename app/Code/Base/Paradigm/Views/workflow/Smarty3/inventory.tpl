{assign var=inventory value=$workflows->inventory()}
{
    "data": {
        "rows":     "{$workflows->_rows()}",
        "rowCount": "{$workflows->_rowCount()}",
        "page":     "{$workflows->_page()}",
        "pages":    "{$workflows->_pages()}",
        "fromRow":  "{$workflows->_fromRow()}",
        "toRow":    "{$workflows->_toRow()}"
    },
    "diagrams": {$inventory}
}
