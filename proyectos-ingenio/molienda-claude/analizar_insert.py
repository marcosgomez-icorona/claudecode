import json

FLOW_PATH = "/mnt/c/claudecode/proyectos-ingenio/molienda-claude/Flujos Node Red/conexion_a_opc_v9.json"
with open(FLOW_PATH) as f:
    flow = json.load(f)
nodes = flow if isinstance(flow, list) else flow.get('flows', flow)

fn_ins = next(n for n in nodes if n.get('type') == 'function' and 'INSERT' in n.get('name',''))
fn_map = next(n for n in nodes if n.get('type') == 'function' and 'tagMap' in n.get('func',''))

cols = [
    'timestamp','vapor_total','potencia_total','velocidad_molino1','velocidad_molino6','balanza_cinta',
    'agua_imbibicion','presion_molino6_este','presion_molino6_oeste','caudal_jugo_clarif',
    'nivel_melado_tratado','nivel_melado','nivel_decantador1','nivel_decantador2','nivel_decantador3',
    'descarga_tachos_1ra','descarga_tachos_2da','descarga_tachos_3ra','contador_bolsas_dia',
    'silo_a','silo_b','silo_c','silo_e','presion_vapor_directo','presion_agua_alim','presion_aire',
    'caudal_vapor_cald1','caudal_vapor_cald2','caudal_vapor_cald3','caudal_vapor_cald6',
    'caudal_gas_cald2','caudal_gas_cald6','potencia_activa_siemens','potencia_reactiva_siemens',
    'frecuencia_siemens','intensidad_siemens','potencia_activa_aeg','potencia_reactiva_aeg',
    'frecuencia_aeg','intensidad_aeg','cv_trapiche','cv_usina_alta','cv_destileria','cv_aux_total',
    'cv_preparacion_cania','potencia_activa_tgm','intensidad_tgm','caudal_vino','nivel_jugo_pesado',
    'nivel_jugo_clarificado','temp_agua_alim','presion_vapor_escape','caudal_agua_dilutor',
    'temp_calentador','presion_vg1','nivel_agua_foza','caudal_vino_160','presion_k2',
    'potencia_activa_edet','intensidad_edet','caudal_alcohol','caudal_jugo_dilutor','caudal_melaza_dilutor'
]

vals_raw = "2026-05-13 17:30:00,147.66000000000003,0,71.73,0,NULL,13,0,0,16,35.71,NULL,-105.11,-105.11,-105.11,7.27,-0.71,-0.27,0,637,992,733,1000,4.41,4.26,28.38,NULL,49.08,49.38,49.2,1236,1236,0,0,0,0,0,0,0,0,65.12,31.04,24.27,24.56,23.38,0,NULL,8,164.23,-22.94,4600,1.54,3176,10348,4.58,-46.04,0,0,0,4290,4,6928,16380"
vals = vals_raw.split(',')

print(f"Cols: {len(cols)}  Vals: {len(vals)}")
print()

print("=== Valores problemáticos ===")
for i, (c, v) in enumerate(zip(cols, vals)):
    flag = ''
    try:
        fv = float(v)
        if fv < 0:
            flag = '<-- NEGATIVO (escalado fuera de rango)'
        elif 'nivel' in c or 'descarga' in c:
            if fv > 110:
                flag = '<-- SOBRE 100% (escalado fuera de rango)'
        elif 'temp' in c and fv > 500:
            flag = '<-- SIN ESCALAR (valor raw Kepserver)'
    except:
        pass
    if flag:
        print(f"  [{i+1:2d}] {c:<35} = {v:>12}  {flag}")

print()
print("=== ON DUPLICATE KEY UPDATE — bugs ===")
func = fn_ins['func']
has_null_jugo  = 'caudal_jugo_dilutor`=NULL'      in func or "caudal_jugo_dilutor`=NULL"  in func
has_null_melaza = 'caudal_melaza_dilutor`=NULL'    in func or "caudal_melaza_dilutor`=NULL" in func
print(f"  caudal_jugo_dilutor  se actualiza con NULL:   {has_null_jugo}")
print(f"  caudal_melaza_dilutor se actualiza con NULL:  {has_null_melaza}")

print()
print("=== SCALING — entradas faltantes ===")
scaling_block = fn_map['func'].split('const SCALING')[1].split('};')[0]
for k in ['temp_agua_alim','temp_calentador','caudal_vino','caudal_alcohol',
          'caudal_jugo_dilutor','caudal_melaza_dilutor','caudal_agua_dilutor']:
    present = f"'{k}'" in scaling_block
    print(f"  {'OK  ' if present else 'FALTA'} — {k}")
