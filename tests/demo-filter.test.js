/** Testes automatizados sem dependências externas para a interação da demonstração pública.
 * Execute: node tests/demo-filter.test.js
 */
const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const path = require('node:path');
const source = fs.readFileSync(path.join(__dirname, '..', 'demo.php'), 'utf8');
const script = source.match(/<script>([\s\S]*?)<\/script>/);
assert.ok(script, 'JavaScript do filtro precisa existir');
function element(value='') {
  return {value,hidden:false,textContent:'',dataset:{},handlers:{},addEventListener(type,callback){this.handlers[type]=callback;},focus(){}};
}
const query = element(), status = element('todos'), count=element(), empty=element();
const data = [
  ['Notebook Dell','disponivel'],
  ['Monitor LG','manutencao'],
  ['Projetor Epson','emprestado'],
];
const rows = data.map(([name, state])=>Object.assign(element(),{dataset:{search:name.toLowerCase(),status:state}}));
const reset = element();
const document = {
  getElementById(id){return {'demo-query':query,'demo-status':status,'demo-count':count,'demo-empty':empty,'demo-reset':reset}[id];},
  querySelectorAll(sel){assert.equal(sel,'.demo-row');return rows;},
};
vm.runInNewContext(script[1], {document});
assert.ok(query.handlers.input && status.handlers.change && reset.handlers.click,'handlers deveriam existir');
query.value='notebook';query.handlers.input();
assert.deepEqual(rows.map(r=>r.hidden), [false,true,true]);
assert.equal(count.textContent,'1 RESULTADOS');
query.value='';status.value='manutencao';status.handlers.change();
assert.deepEqual(rows.map(r=>r.hidden), [true,false,true]);
query.value='algo inexistente';query.handlers.input();
assert.equal(empty.hidden,false);
reset.handlers.click();
assert.deepEqual(rows.map(r=>r.hidden), [false,false,false]);
assert.equal(count.textContent,'3 RESULTADOS');
console.log('OK — filtros por texto e status, vazio e limpar filtros.');
