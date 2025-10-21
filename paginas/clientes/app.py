# app.py (Versión API)
import os
from flask import Flask, request, jsonify
from flask_sqlalchemy import SQLAlchemy
from dotenv import load_dotenv

load_dotenv()

app = Flask(__name__)
# ... (La configuración de la base de datos y los modelos de SQLAlchemy permanecen igual) ...
DATABASE_URL = os.getenv("DATABASE_URL").replace("postgres://", "postgresql://", 1)
app.config['SQLALCHEMY_DATABASE_URI'] = DATABASE_URL
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db = SQLAlchemy(app)

class Gira(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(255), nullable=False, unique=True)
    descripcion = db.Column(db.Text)
    clientes = db.relationship('Cliente', backref='gira', lazy=True)

class Cliente(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    codigo = db.Column(db.String(50), nullable=False, unique=True)
    nombre = db.Column(db.String(255), nullable=False)
    telefono = db.Column(db.String(50), nullable=False)
    nit = db.Column(db.String(50))
    direccion = db.Column(db.String(255))
    latitud = db.Column(db.Numeric(10, 8))
    longitud = db.Column(db.Numeric(11, 8))
    observaciones = db.Column(db.Text)
    id_gira = db.Column(db.Integer, db.ForeignKey('gira.id'), nullable=True)

# --- RUTAS DE LA API ---

# Endpoint para obtener todas las giras
@app.route('/api/giras', methods=['GET'])
def get_giras():
    giras = Gira.query.order_by(Gira.nombre).all()
    return jsonify([{'id': g.id, 'nombre': g.nombre} for g in giras])

# Endpoint para obtener todos los clientes
@app.route('/api/clientes', methods=['GET'])
def get_clientes():
    clientes = Cliente.query.order_by(Cliente.nombre).all()
    # Convertimos los objetos de SQLAlchemy a un formato JSON
    resultado = []
    for c in clientes:
        resultado.append({
            'id': c.id, 'codigo': c.codigo, 'nombre': c.nombre, 'telefono': c.telefono,
            'nit': c.nit, 'direccion': c.direccion, 'observaciones': c.observaciones,
            'latitud': float(c.latitud) if c.latitud else None,
            'longitud': float(c.longitud) if c.longitud else None,
            'nombre_gira': c.gira.nombre if c.gira else None
        })
    return jsonify(resultado)

# Endpoint para crear un nuevo cliente
@app.route('/api/clientes', methods=['POST'])
def create_cliente():
    data = request.get_json()
    try:
        nuevo_cliente = Cliente(
            codigo=data['codigo'], nombre=data['nombre'], telefono=data['telefono'],
            nit=data.get('nit'), direccion=data.get('direccion'),
            latitud=data.get('latitud'), longitud=data.get('longitud'),
            id_gira=data.get('id_gira'), observaciones=data.get('observaciones')
        )
        db.session.add(nuevo_cliente)
        db.session.commit()
        return jsonify({'message': 'Cliente creado con éxito', 'id': nuevo_cliente.id}), 201
    except Exception as e:
        db.session.rollback()
        return jsonify({'error': str(e)}), 400

if __name__ == '__main__':
    app.run(debug=True)