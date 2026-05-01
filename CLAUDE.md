# NutriSphere Backend

## Knowledge Graph (RAG)

A graphify knowledge graph lives at `graphify-out/`. **Before answering questions about architecture, data flow, or relationships between modules, check this graph first.**

### Files to consult

| File | Use it for |
|------|-----------|
| `graphify-out/graph.json` | Full node/edge graph — query programmatically for traversal |
| `graphify-out/GRAPH_REPORT.md` | Human-readable summary: god nodes, communities, surprising connections |

### How to use it

**Quick lookup (read the report):**
```bash
cat graphify-out/GRAPH_REPORT.md
```

**Find a specific concept (grep nodes):**
```bash
python3 -c "
import json
from pathlib import Path
g = json.loads(Path('graphify-out/graph.json').read_text())
term = 'YOUR_TERM'
hits = [n for n in g['nodes'] if term.lower() in n.get('label','').lower()]
for h in hits:
    print(h['id'], '-', h.get('label'), '|', h.get('source_file',''))
"
```

**BFS traversal from a node (broad context):**
```bash
python3 -c "
import json
import networkx as nx
from networkx.readwrite import json_graph
from pathlib import Path

data = json.loads(Path('graphify-out/graph.json').read_text())
G = json_graph.node_link_graph(data, edges='links')
term = 'YOUR_NODE_LABEL'

scored = sorted(
    [(sum(1 for w in term.lower().split() if w in G.nodes[n].get('label','').lower()), n)
     for n in G.nodes()], reverse=True)
start = scored[0][1] if scored and scored[0][0] > 0 else None
if start:
    visited = set([start])
    frontier = [start]
    for _ in range(2):
        nxt = []
        for n in frontier:
            for nb in G.neighbors(n):
                if nb not in visited:
                    visited.add(nb)
                    nxt.append(nb)
                    e = G.edges[n, nb]
                    print(G.nodes[n].get('label',n), '--', e.get('relation',''), '-->', G.nodes[nb].get('label',nb))
        frontier = nxt
"
```

**Shortest path between two concepts:**
```bash
python3 -c "
import json, networkx as nx
from networkx.readwrite import json_graph
from pathlib import Path

data = json.loads(Path('graphify-out/graph.json').read_text())
G = json_graph.node_link_graph(data, edges='links')

def find(term):
    return sorted([(sum(1 for w in term.lower().split() if w in G.nodes[n].get('label','').lower()), n) for n in G.nodes()], reverse=True)[0][1]

src, tgt = find('CONCEPT_A'), find('CONCEPT_B')
path = nx.shortest_path(G, src, tgt)
for i, n in enumerate(path):
    print(G.nodes[n].get('label', n))
    if i < len(path)-1:
        e = G.edges[n, path[i+1]]
        print('  --', e.get('relation',''), '-->')
"
```

### When the graph is stale

If you've added/changed files and the graph feels out of date, run:
```
/graphify . --update
```

to incrementally re-extract only changed files without rebuilding from scratch.

### Building the graph (first time or full rebuild)

Run `/graphify ./app` (or a specific subfolder) to build the graph. The vendor/ directory should be excluded — it's just composer dependencies and will bloat the graph without adding insight.

Recommended scope:
```
/graphify ./app
```
or for full project context (no vendor):
```
/graphify .  # graphify auto-skips vendor/ via .gitignore patterns
```
